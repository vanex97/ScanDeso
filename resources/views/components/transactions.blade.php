<div class="table-responsive mb-3">
    <table class="table table-bordered mt-5 mb-0">
        <thead>
        <tr>
            <th scope="col">Transaction ID</th>
            <th scope="col">Type</th>
            <th scope="col">From</th>
            @if($user)
                <th scope="col"></th>
            @endif
            <th scope="col">To</th>
            <th scope="col">Value</th>
            <th scope="col">Fee Nanos</th>
        </tr>
        </thead>
        <tbody>
        @foreach($transactions as $transaction)
            @if(!$transaction['TransactionMetadata']['TxnOutputs'])
                @continue
            @endif

            <tr>
                <td>
                    <a class="text-truncate hash-tag" href="{{ route('transaction', ['transactionId' => $transaction['TransactionIDBase58Check']]) }}">
                        {{ $transaction['TransactionIDBase58Check'] }}
                    </a>
                </td>
                <td>
                    {{ \App\Helpers\StringHelper::formatTransactionType($transaction['TransactionType']) }}
                    @php $transactionSubType = \App\Helpers\TransactionHelper::getSubtype($transaction) @endphp

                    @if(isset($transaction['ExtraData']['DiamondPostHash']) && $transaction['TransactionType'] == 'BASIC_TRANSFER')
                        💎
                    @endif

                    @if($transactionSubType)
                        ({{ $transactionSubType }})
                    @endif
                </td>
                <td>
                    @if ($userKeyToUsername->has($transaction['TransactionMetadata']['TransactorPublicKeyBase58Check']))
                        <a href="{{ route('address', $userKeyToUsername->get($transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'])) }}"
                           class="text-truncate hash-tag"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           data-bs-custom-class="address-tooltip"
                           data-bs-title="{{ $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'] }}">
                            {{ $userKeyToUsername->get($transaction['TransactionMetadata']['TransactorPublicKeyBase58Check']) }}
                        </a>
                    @else
                        <a href="{{ route('address', $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check']) }}"
                           class="text-truncate hash-tag"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           data-bs-custom-class="address-tooltip"
                           data-bs-title="{{ $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'] }}">
                            {{ $transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'] }}
                        </a>
                    @endif
                </td>
                @if($user)
                    <td class="text-center">
                        @if(isset($transaction['TransactionMetadata']['CreatorCoinTxindexMetadata']['OperationType']))
                            @if($transaction['TransactionMetadata']['CreatorCoinTxindexMetadata']['OperationType'] == 'buy')
                                <span class="badge rounded-pill text-bg-success operation-badge">Buy</span>
                            @elseif($transaction['TransactionMetadata']['CreatorCoinTxindexMetadata']['OperationType'] == 'sell')
                                <span class="badge rounded-pill text-bg-danger operation-badge">Sell</span>
                            @else
                                <span class="badge rounded-pill text-bg-primary operation-badge">
                                        {{ ucfirst($transaction['TransactionMetadata']['CreatorCoinTxindexMetadata']['OperationType']) }}
                                    </span>
                            @endif
                        @elseif($transaction['TransactionMetadata']['TransactorPublicKeyBase58Check'] == $user['PublicKeyBase58Check'])
                            <span class="badge rounded-pill text-bg-warning operation-badge">Out</span>
                        @else
                            <span class="badge rounded-pill text-bg-success operation-badge">In</span>
                        @endif
                    </td>
                @endif
                <td>
                    @php $transactionInputs = \App\Helpers\TransactionHelper::getTransferInputs($transaction['TransactionMetadata']['AffectedPublicKeys']); @endphp

                    @if(!$transactionInputs || $transaction['TransactionType'] == 'UPDATE_PROFILE')
                        -
                    @elseif(count($transactionInputs) == 1)
                        @if ($userKeyToUsername->has($transactionInputs[0]['PublicKeyBase58Check']))
                            <a href="{{ route('address', $userKeyToUsername->get($transactionInputs[0]['PublicKeyBase58Check'])) }}"
                               class="text-truncate hash-tag"
                               data-bs-toggle="tooltip"
                               data-bs-placement="top"
                               data-bs-custom-class="address-tooltip"
                               data-bs-title="{{ $transactionInputs[0]['PublicKeyBase58Check'] }}">
                                {{ $userKeyToUsername->get($transactionInputs[0]['PublicKeyBase58Check']) }}
                            </a>
                        @else
                            <a href="{{ route('address', $transactionInputs[0]['PublicKeyBase58Check']) }}"
                               class="text-truncate hash-tag"
                               data-bs-toggle="tooltip"
                               data-bs-placement="top"
                               data-bs-custom-class="address-tooltip"
                               data-bs-title="{{ $transactionInputs[0]['PublicKeyBase58Check'] }}">
                                {{ $transactionInputs[0]['PublicKeyBase58Check'] }}
                            </a>
                        @endif
                    @elseif(count($transactionInputs) >= 2)
                        <div class="accordion accordion-flush" id="accordion-{{ $loop->iteration }}">
                            <div class="accordion-item">
                                <button class="accordion-button p-0 collapsed"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#flush-collapse-{{ $loop->iteration }}"
                                        aria-controls="flush-collapse-{{ $loop->iteration }}">
                                    {{ count($transactionInputs) }} addresses @if($transaction['TransactionType'] == 'SUBMIT_POST') mentioned @endif
                                </button>
                                <div id="flush-collapse-{{ $loop->iteration }}"
                                     class="collapse clickable"
                                     aria-labelledby="flush-heading-{{ $loop->iteration }}"
                                     data-bs-parent="#accordion-{{ $loop->iteration }}">
                                    @foreach($transactionInputs as $affectedTransaction)
                                        <div class="row mt-1">
                                            @if($userKeyToUsername->has($affectedTransaction['PublicKeyBase58Check']))
                                                <a href="{{ route('address', $userKeyToUsername->get($affectedTransaction['PublicKeyBase58Check'])) }}"
                                                   class="text-truncate hash-tag"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   data-bs-custom-class="address-tooltip"
                                                   data-bs-title="{{ $affectedTransaction['PublicKeyBase58Check'] }}">
                                                    {{ $userKeyToUsername->get($affectedTransaction['PublicKeyBase58Check']) }}
                                                </a>
                                            @else
                                                <a href="{{ route('address', $affectedTransaction['PublicKeyBase58Check']) }}"
                                                   class="text-truncate hash-tag"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   data-bs-custom-class="address-tooltip"
                                                   data-bs-title="{{ $affectedTransaction['PublicKeyBase58Check'] }}">
                                                    {{ $affectedTransaction['PublicKeyBase58Check'] }}
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </td>

                @if(\App\Helpers\TransactionHelper::getValueByType($transaction))
                    <td data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        data-bs-title="{{ \App\Helpers\TransactionHelper::getValueByType($transaction, null) }} {{ \App\Helpers\TransactionHelper::getTickerForValueByType($transaction) }}">
                        {{ \App\Helpers\TransactionHelper::getValueByType($transaction) }}
                    </td>
                @else
                    <td>-</td>
                @endif
                <td>
                    {{ \App\Helpers\CurrencyHelper::nanoToDeso($transaction['TransactionMetadata']['BasicTransferTxindexMetadata']['FeeNanos'], 7) }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
