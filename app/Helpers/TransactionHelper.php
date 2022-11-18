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

    // TODO
    public static function getPostOrNftLink($transaction)
    {

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
}
