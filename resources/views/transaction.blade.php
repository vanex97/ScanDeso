@extends('layouts.main')

@section('title', 'Transaction details')

@section('content')
    <div class="mt-3 mb-5">
        <x-search></x-search>
    </div>

    <div class="d-flex justify-content-between">
        <h1 class="h2 mb-3">Transaction details</h1>
        <div>
        @if($lastTransaction)
            <a href="{{ route('transaction', ['transactionId' => $lastTransaction]) }}"
               class="btn btn-outline-primary"
               data-bs-toggle="tooltip"
               data-bs-placement="top"
               data-bs-custom-class="address-tooltip"
               data-bs-title="Last transaction for {{ $transactorProfile['Username'] }}">
                Last transaction
            </a>
        @endif
        </div>
    </div>

    <div class="border info-table">
        <div class="row m-2 mb-3">
            <div class="col-md-3">Transaction ID:</div>
            <div class="col-md-9 text-break" id="transaction">{{ $transaction['TransactionIDBase58Check'] }}</div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-md-3">Transaction Type:</div>
            <div class="col-md-9 text-break">
                {{ \App\Helpers\StringHelper::formatTransactionType($transaction['TransactionType']) }}

                @php $transactionSubType = \App\Helpers\TransactionHelper::getSubtype($transaction) @endphp

                @if ($transactionSubType)
                    ({{ $transactionSubType }})
                @endif

                @php
                    $postHash = \App\Helpers\TransactionHelper::getPostHash($transaction);
                    $nftHash = \App\Helpers\TransactionHelper::getNFTHash($transaction);
                @endphp

                @if($postHash)
                    (<a href="https://diamondapp.com/posts/{{ $postHash }}" target="_blank">link to post</a>)
                @elseif($nftHash)
                    (<a href="https://diamondapp.com/nft/{{ $nftHash }}" target="_blank">link to nft</a>,
                    <a href="https://nftz.me/posts/{{ $nftHash }}" target="_blank">nftz</a>)
                @endif
            </div>
        </div>
        @if(isset($transaction['ExtraData']['DiamondLevel']))
            <div class="row m-2 mb-3">
                <div class="col-md-3">Diamond level:</div>
                <div class="col-md-9 text-break">
                    {{ str_repeat('💎', $transaction['ExtraData']['DiamondLevel']) }}
                </div>
            </div>
        @endif
        <div class="border-bottom"></div>
        <div class="row m-2 mb-3">
            <div class="col-md-3">Block:</div>
            <div id="blockHeight" class="col-md-9 text-break">
                @if(isset($block['Height']))
                    <a href="{{ route('block', ['block' => $block['Height']]) }}">{{ $block['Height'] }}</a>
                @else
                    Pending...
                    <div id="block-load" class="spinner-border spinner-border-sm text-primary" role="status"></div>
                @endif
            </div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-md-3">Txn index in block:</div>
            <div id="tnxIndex" class="col-md-9 text-break">
                @if($block)
                    {{ $transaction['TransactionMetadata']['TxnIndexInBlock'] }}
                @else
                    Pending...
                @endif
            </div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-md-3">Timestamp:</div>
            <div id="timestamp" class="col-md-9 text-break" data-timestamp="{{ $block['TstampSecs'] ?? '' }}">
                @if(isset($block['TstampSecs']))
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                @else
                    Pending...
                @endif
            </div>
        </div>
        @if(isset($transaction['TransactionMetadata']['DAOCoinTransferTxindexMetadata']))
            <div class="border-bottom"></div>
            <div class="row m-2 mb-3">
                <div class="col-md-3">DAO Creator:</div>
                <div class="col-md-9 text-break">
                    <a href="https://daodao.io/d/{{ $transaction['TransactionMetadata']['DAOCoinTransferTxindexMetadata']['CreatorUsername'] }}" target="_blank">
                        {{ $transaction['TransactionMetadata']['DAOCoinTransferTxindexMetadata']['CreatorUsername'] }}
                    </a>
                </div>
            </div>
            <div class="row m-2 mb-3">
                <div class="col-md-3">DAO Coins:</div>
                <div class="col-md-9 text-break">
                    ≈ {{ \App\Helpers\CurrencyHelper::hexdecToDecimal($transaction['TransactionMetadata']['DAOCoinTransferTxindexMetadata']['DAOCoinToTransferNanos']) }}
                </div>
            </div>
        @endif
        @if(isset($transaction['TransactionMetadata']['CreatorCoinTxindexMetadata']['DESOLockedNanosDiff']))
            <div class="border-bottom"></div>
            <div class="row m-2 mb-3">
                <div class="col-md-3">Creator coin value:</div>
                <div class="col-md-9 text-break">
                    {{ \App\Helpers\CurrencyHelper::nanoToDeso(abs($transaction['TransactionMetadata']['CreatorCoinTxindexMetadata']['DESOLockedNanosDiff']), null) }} DESO
                    (${{ \App\Helpers\CurrencyHelper::nanoToDollars(abs($transaction['TransactionMetadata']['CreatorCoinTxindexMetadata']['DESOLockedNanosDiff']), $desoDesoPrice, 8) }})
                </div>
            </div>
        @endif
        @if(isset($transaction['TransactionMetadata']['CreatorCoinTransferTxindexMetadata']))
            <div class="border-bottom"></div>
            <div class="row m-2 mb-3">
                <div class="col-md-3">Creator Creator:</div>
                <div class="col-md-9 text-break">
                    <a href="https://diamondapp.com/u/{{ $transaction['TransactionMetadata']['CreatorCoinTransferTxindexMetadata']['CreatorUsername'] }}" target="_blank">
                        {{ $transaction['TransactionMetadata']['CreatorCoinTransferTxindexMetadata']['CreatorUsername'] }}
                    </a>
                </div>
            </div>
            <div class="row m-2 mb-3">
                <div class="col-md-3">Creator Coins:</div>
                <div class="col-md-9 text-break">
                    {{ \App\Helpers\CurrencyHelper::nanoToDeso($transaction['TransactionMetadata']['CreatorCoinTransferTxindexMetadata']['CreatorCoinToTransferNanos'], null) }}
                </div>
            </div>
        @endif
        @if(isset($transaction['TransactionMetadata']['NFTBidTxindexMetadata']))
            <div class="border-bottom"></div>
            <div class="row m-2 mb-3">
                <div class="col-md-3">Serial number:</div>
                <div class="col-md-9 text-break">{{ $transaction['TransactionMetadata']['NFTBidTxindexMetadata']['SerialNumber'] }}</div>
            </div>
            <div class="row m-2 mb-3">
                <div class="col-md-3">Bid Amount:</div>
                <div class="col-md-9 text-break">
                    {{ \App\Helpers\CurrencyHelper::nanoToDeso($transaction['TransactionMetadata']['NFTBidTxindexMetadata']['BidAmountNanos'], null) }} DESO
                    (${{ \App\Helpers\CurrencyHelper::nanoToDollars($transaction['TransactionMetadata']['NFTBidTxindexMetadata']['BidAmountNanos'], $desoDesoPrice, 8) }})
                </div>
            </div>
        @endif
        @if(isset($transaction['TransactionMetadata']['AcceptNFTBidTxindexMetadata']))
            <div class="border-bottom"></div>
            <div class="row m-2 mb-3">
                <div class="col-md-3">Serial number:</div>
                <div class="col-md-9 text-break">{{ $transaction['TransactionMetadata']['AcceptNFTBidTxindexMetadata']['SerialNumber'] }}</div>
            </div>
            <div class="row m-2 mb-3">
                <div class="col-md-3">Bid Amount:</div>
                <div class="col-md-9 text-break">
                    {{ \App\Helpers\CurrencyHelper::nanoToDeso($transaction['TransactionMetadata']['AcceptNFTBidTxindexMetadata']['BidAmountNanos'], null) }} DESO
                    (${{ \App\Helpers\CurrencyHelper::nanoToDollars($transaction['TransactionMetadata']['AcceptNFTBidTxindexMetadata']['BidAmountNanos'], $desoDesoPrice, 8) }})
                </div>
            </div>
        @endif
        <div class="border-bottom"></div>
        <div class="row m-2 mb-3">
            <div class="col-md-3">From:</div>
            <div class="col-md-9 text-break">
                <a class="me-2" href="{{ route('address', $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check']) }}">
                    {{ $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'] }}
                </a>
                (<a href="https://diamondapp.com/u/{{ $transactorProfile['Username'] }}" target="_blank">{{ $transactorProfile['Username'] }}</a>)
            </div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-md-3">To:</div>
            <div class="col-md-9 text-break">

                @php $transactionInputs = \App\Helpers\TransactionHelper::getTransferInputs($transaction['TransactionMetadata']['AffectedPublicKeys']); @endphp

                @if(!$transactionInputs || $transaction['TransactionType'] == 'UPDATE_PROFILE')
                    -
                @elseif(count($transactionInputs) == 1)
                    <a href="{{ route('address', $transactionInputs[0]['PublicKeyBase58Check']) }}">
                        {{ $transactionInputs[0]['PublicKeyBase58Check'] }}
                    </a>
                @elseif(count($transactionInputs) >= 2)
                    <div class="accordion accordion-flush" id="accordion">
                        <div class="accordion-item">
                            <button class="accordion-button p-0 collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapse"
                                    aria-controls="flush-collapse">
                                {{ count($transactionInputs) }} addresses @if($transaction['TransactionType'] == 'SUBMIT_POST') mentioned @endif
                            </button>
                            <div id="flush-collapse"
                                 class="collapse clickable"
                                 aria-labelledby="flush-heading"
                                 data-bs-parent="#accordion">
                                @foreach($transactionInputs as $affectedTransaction)
                                    <div class="row mt-1">
                                        <a href="{{ route('address', $affectedTransaction['PublicKeyBase58Check']) }}">
                                            {{ $affectedTransaction['PublicKeyBase58Check'] }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="border-bottom"></div>
        <div class="row m-2 mb-3">
            <div class="col-md-3">Value:</div>
            <div class="col-md-9 text-break">
                {{ \App\Helpers\CurrencyHelper::nanoToDeso(\App\Helpers\TransactionHelper::getValue($transaction), null) }} DESO
                (${{ \App\Helpers\CurrencyHelper::nanoToDollars(\App\Helpers\TransactionHelper::getValue($transaction), $desoDesoPrice, 8) }})
            </div>
        </div>
        <div class="row m-2 mb-3">
            <div class="col-md-3">Transaction Fee:</div>
            <div class="col-md-9 text-break">
                {{ \App\Helpers\CurrencyHelper::nanoToDeso($transaction['TransactionMetadata']['BasicTransferTxindexMetadata']['FeeNanos'], 7) }} DESO
                (${{ \App\Helpers\CurrencyHelper::nanoToDollars($transaction['TransactionMetadata']['BasicTransferTxindexMetadata']['FeeNanos'], $desoDesoPrice, 8) }})
            </div>
        </div>
        <a class="row m-2 mb-2" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
            <div class="col-md-3">
                Show more...
            </div>
        </a>
        <div class="collapse" id="collapseExample">

            @php $NFTRoyaltiesMetadata = \App\Helpers\TransactionHelper::getNFTRoyaltiesMetadata($transaction); @endphp

            @if($NFTRoyaltiesMetadata)
                <div class="row m-2 mb-3 table-responsive">
                    <h4>NFT Royalties:</h4>
                    <table class="table ms-2 me-2">
                        <thead>
                        <tr>
                            <th style="width: 70%" scope="col">Name</th>
                            <th scope="col">Value</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Creator Coin Royalty</td>
                            <td>{{ \App\Helpers\CurrencyHelper::nanoToDeso($NFTRoyaltiesMetadata['CreatorCoinRoyaltyNanos']) }} DESO</td>
                        </tr>
                        <tr>
                            <td>Creator Royalty</td>
                            <td>{{ \App\Helpers\CurrencyHelper::nanoToDeso($NFTRoyaltiesMetadata['CreatorRoyaltyNanos']) }} DESO</td>
                        </tr>
                        <tr>
                            @if($NFTRoyaltiesMetadata['CreatorPublicKeyBase58Check'])
                                <td>Creator Public Key</td>
                                <td>
                                    <a class="text-truncate hash-tag"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       data-bs-custom-class="address-tooltip"
                                       data-bs-title="{{ $NFTRoyaltiesMetadata['CreatorPublicKeyBase58Check'] }}"
                                       href="{{ route('address', ['address' => $NFTRoyaltiesMetadata['CreatorPublicKeyBase58Check']]) }}">
                                        {{ $NFTRoyaltiesMetadata['CreatorPublicKeyBase58Check'] }}
                                    </a>
                                </td>
                            @endif
                        </tr>
                        @if(isset($NFTRoyaltiesMetadata['AdditionalDESORoyaltiesMap']))
                            <tr>
                                <td><b>Additional DESO Royalties Map:</b></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>Address</td>
                                <td>Value</td>
                            </tr>
                            @foreach($NFTRoyaltiesMetadata['AdditionalDESORoyaltiesMap'] as $address => $value)
                                <tr>
                                    <td>
                                        <a href="{{ route('address', ['address' => $address]) }}">
                                            {{ $address }}
                                        </a>
                                    </td>
                                    <td>{{ \App\Helpers\CurrencyHelper::nanoToDeso($value) }} DESO</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            @endif
            <div class="row m-2 mb-3 table-responsive">
                <h4>Transaction Inputs:</h4>
                <table class="table ms-2 me-2">
                    <thead>
                        <tr>
                            <th style="width: 70%" scope="col">TransactionIDBase58Check</th>
                            <th scope="col">Index</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transaction['Inputs'] as $input)
                            <tr>
                                <td>
                                    <a href="{{ route('transaction', ['transactionId' => $input['TransactionIDBase58Check']]) }}">
                                        {{ $input['TransactionIDBase58Check'] }}
                                    </a>
                                </td>
                                <td>{{ $input['Index'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row m-2 mb-3 table-responsive">
                <h4>Transaction Outputs:</h4>
                <table class="table ms-2 me-2">
                    <thead>
                    <tr>
                        <th style="width: 70%" scope="col">PublicKeyBase58Check</th>
                        <th scope="col">Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transaction['Outputs'] as $input)
                        <tr>
                            <td>
                                <a class="me-2" href="{{ route('address', $input['PublicKeyBase58Check']) }}">
                                    {{ $input['PublicKeyBase58Check'] }}
                                </a>
                            </td>
                            <td>{{ \App\Helpers\CurrencyHelper::nanoToDeso($input['AmountNanos'], null) }} DESO</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row m-2 mb-3 table-responsive">
                <h4>Affected Public Keys:</h4>
                <table class="table ms-2 me-2">
                    <thead>
                    <tr>
                        <th style="width: 70%" scope="col">PublicKeyBase58Check</th>
                        <th scope="col">Metadata</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transaction['TransactionMetadata']['AffectedPublicKeys'] as $input)
                        <tr>
                            <td>
                                <a class="me-2" href="{{ route('address', $input['PublicKeyBase58Check']) }}">
                                    {{ $input['PublicKeyBase58Check'] }}
                                </a>
                            </td>
                            <td>{{ $input['Metadata'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
