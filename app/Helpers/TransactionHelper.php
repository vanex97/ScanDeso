<?php

namespace App\Helpers;

class TransactionHelper
{
    public const POST_URI = 'https://diamondapp.com/posts/';
    public const NFT_URI = 'https://nftz.me/posts/';

    public static function getTransferInputs($affectedPublicKeys)
    {
        $inputsPublicKeys = [];

        foreach ($affectedPublicKeys as $affectedPublicKey) {
            if ($affectedPublicKey['Metadata'] !== 'BasicTransferOutput') {
                $inputsPublicKeys[] = $affectedPublicKey;
            }

            if (in_array($affectedPublicKey['Metadata'], ['NFTOwnerPublicKeyBase58Check', 'NFTBidderPublicKeyBase58Check'])) {
                return [$affectedPublicKey];
            }
        }

        if (!count($inputsPublicKeys) && count($affectedPublicKeys) > 1) {
            return array_slice($affectedPublicKeys, 0, 1);
        }

        return $inputsPublicKeys;
    }

    public static function getValue($transaction)
    {
        if (isset($transaction['TransactionMetadata']['NFTBidTxindexMetadata']['BidAmountNanos'])) {
            return $transaction['TransactionMetadata']['NFTBidTxindexMetadata']['BidAmountNanos'];
        }

        return $transaction['Outputs'][0]['AmountNanos'];
    }

    public static function getValueByType($transaction, $decimals = 2)
    {
        $value = self::issetValueFromTransaction($transaction, [
            ['TransactionMetadata', 'NFTBidTxindexMetadata', 'BidAmountNanos'],
            ['TransactionMetadata', 'DAOCoinTransferTxindexMetadata', 'DAOCoinToTransferNanos'],
            ['TransactionMetadata', 'CreatorCoinTxindexMetadata', 'DESOLockedNanosDiff'],
            ['TransactionMetadata', 'CreatorCoinTransferTxindexMetadata', 'CreatorCoinToTransferNanos'],
            ['TransactionMetadata', 'AcceptNFTBidTxindexMetadata', 'BidAmountNanos']
        ]);

        if ($value === null && $transaction['TransactionType'] == 'BASIC_TRANSFER') {
            $value = $transaction['Outputs'][0]['AmountNanos'];
        }


        if (isset($transaction['TransactionMetadata']['DAOCoinTransferTxindexMetadata']['DAOCoinToTransferNanos'])) {
            return CurrencyHelper::hexdecToDecimal($value);
        }

        return $value ? CurrencyHelper::nanoToDeso(abs($value), $decimals) : null;
    }

    public static function getTickerForValueByType($transaction)
    {
        $valueTickerPaths = [
            ['TransactionMetadata', 'DAOCoinTransferTxindexMetadata', 'CreatorUsername'],
            ['TransactionMetadata', 'CreatorCoinTransferTxindexMetadata', 'CreatorUsername']
        ];
        $valueTicker = self::issetValueFromTransaction($transaction, $valueTickerPaths);

        if ($valueTicker) {
            return $valueTicker;
        }

        return 'DESO';
    }

    public static function getNFTRoyaltiesMetadata($transaction)
    {
        if (isset($transaction['TransactionMetadata']['AcceptNFTBidTxindexMetadata']['NFTRoyaltiesMetadata'])) {
            return $transaction['TransactionMetadata']['AcceptNFTBidTxindexMetadata']['NFTRoyaltiesMetadata'];
        }

        if (isset($transaction['TransactionMetadata']['NFTBidTxindexMetadata']['NFTRoyaltiesMetadata'])) {
            return $transaction['TransactionMetadata']['NFTBidTxindexMetadata']['NFTRoyaltiesMetadata'];
        }

        return null;
    }

    public static function getSubtype($transaction)
    {
        $subtypesToKeysPath = [
            [
                'subType' => 'Unfollow',
                'keys' => ['FollowTxindexMetadata', 'IsUnfollow']
            ],
            [
                'subType' => 'Unlike',
                'keys' => ['LikeTxindexMetadata', 'IsUnlike']
            ],
            [
                'subType' => ['Open sales', 'Closed sales'],
                'keys' => ['UpdateNFTTxindexMetadata', 'IsForSale']
            ],
            [
                'subType' => 'Buy now',
                'keys' => ['NFTBidTxindexMetadata', 'IsBuyNowBid']
            ],
        ];

        foreach ($subtypesToKeysPath as $subtypeToKeys) {
            $subTitleValue = $transaction['TransactionMetadata'][$subtypeToKeys['keys'][0]][$subtypeToKeys['keys'][1]] ?? null;

            if ($subTitleValue === null) {
                continue;
            }

            if (is_array($subtypeToKeys['subType'])) {
                return $subTitleValue ? $subtypeToKeys['subType'][0] : $subtypeToKeys['subType'][1];
            }

            if ($subTitleValue) {
                return $subtypeToKeys['subType'];
            }
        }

        return null;
    }

    public static function getPostHash($transaction)
    {
        return self::issetValueFromTransaction($transaction, [
            ['TransactionMetadata', 'LikeTxindexMetadata', 'PostHashHex'],
            ['TransactionMetadata', 'SubmitPostTxindexMetadata', 'PostHashBeingModifiedHex'],
            ['ExtraData', 'DiamondPostHash']
        ]);
    }

    public static function getNFTHash($transaction)
    {
        return self::issetValueFromTransaction($transaction, [
            ['TransactionMetadata', 'NFTTransferTxindexMetadata', 'NFTPostHashHex'],
            ['TransactionMetadata', 'AcceptNFTTransferTxindexMetadata', 'NFTPostHashHex'],
            ['TransactionMetadata', 'UpdateNFTTxindexMetadata', 'NFTPostHashHex'],
            ['TransactionMetadata', 'CreateNFTTxindexMetadata', 'NFTPostHashHex'],
            ['TransactionMetadata', 'NFTBidTxindexMetadata', 'NFTPostHashHex'],
            ['TransactionMetadata', 'AcceptNFTBidTxindexMetadata', 'NFTPostHashHex'],
        ]);
    }

    public static function issetValueFromTransaction($transaction, $keyPaths)
    {

        foreach($keyPaths as $keyPath) {
            if (count($keyPath) == 2 && isset($transaction[$keyPath[0]][$keyPath[1]])) {
                return $transaction[$keyPath[0]][$keyPath[1]];
            }

            if (count($keyPath) == 3 && isset($transaction[$keyPath[0]][$keyPath[1]][$keyPath[2]])) {
                return $transaction[$keyPath[0]][$keyPath[1]][$keyPath[2]];
            }
        }

        return null;
    }
}
