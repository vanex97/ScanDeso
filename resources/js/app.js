import {Tooltip} from "bootstrap";
import axios from 'axios';
import moment from 'moment';

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new Tooltip(tooltipTriggerEl)
})

const searchForm = document.querySelector('#search-form');

if (searchForm) {
    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();

        let searchQuery = searchForm.querySelector('.main-search__input').value.trim();

        if(!searchQuery) {
            return;
        }

        if (searchQuery.startsWith('3Ju')) {
            let uri = searchForm.dataset.transaction.slice(0, searchForm.dataset.transaction.length - 1);

            window.open(uri + searchQuery, '_self');
            return;
        }

        window.open(searchForm.dataset.address + '/' + searchQuery, '_self');
    })
}

const timestamp = document.querySelector('#timestamp');

if (timestamp) {
    if (timestamp.dataset.timestamp) {
        setTimeFromTimestamp(
            document.querySelector('#timestamp'),
            timestamp.dataset.timestamp
        );
    }
}

const blockLoad = document.querySelector('#block-load');
const transactionId = document.querySelector('#transaction');

if (blockLoad && transactionId) {
    transactionInfo();
}

function transactionInfo() {
    axios.post('https://node.deso.org/api/v1/transaction-info', {
        TransactionIDBase58Check: transactionId.innerHTML
    })
        .then(function (response) {
            let blockHashHex = response.data?.Transactions[0]?.TransactionMetadata?.BlockHashHex;

            if (blockHashHex) {
                console.log(blockHashHex);

                document.querySelector('#tnxIndex').innerHTML = response.data?.Transactions[0]?.TransactionMetadata?.TxnIndexInBlock;
                setBlockInfo(blockHashHex);
                return;
            }

            setTimeout(transactionInfo, 5000);
        })
        .catch(function (error) {
            setTimeout(transactionInfo, 5000);
        });
}

function setBlockInfo(hashHex) {
    axios.post('https://node.deso.org/api/v1/block', {
            HashHex: hashHex
        })
        .then(function (response) {
            document.querySelector('#blockHeight').innerHTML =
                `<a href="/block/${response.data.Header.Height}">${response.data.Header.Height}</a>`

            setTimeFromTimestamp(
                document.querySelector('#timestamp'),
                response.data.Header.TstampSecs
            );

        })
        .catch(function (error) {
            location.reload();
        });
}

function setTimeFromTimestamp(element, timestamp) {
    let transactionTime = moment.unix(timestamp).utc();
    let transactionDate = `(${transactionTime.format('YYYY-MM-DD HH:mm:ss')} +UTC)`

    element.innerHTML = transactionTime.fromNow() + ' ' + transactionDate;
}

// change theme
const changeTheme = document.querySelector('#changeTheme');

if (changeTheme) {
    const lightHref = '/css/bootstrap.min.css';
    const darkHref = '/css/bootstrap-cyborg.min.css';
    const mainStyleLinkNew = document.querySelector('#mainStyle');

    changeTheme.addEventListener('click', function () {
        if (mainStyleLinkNew.dataset.theme !== 'light') {
            mainStyleLinkNew.href = lightHref;
            mainStyleLinkNew.dataset.theme = 'light';

            changeTheme.querySelector('.light').classList.remove('d-none');
            changeTheme.querySelector('.dark').classList.add('d-none');

            document.body.classList.add('light');
            document.body.classList.remove('dark');

        } else {
            mainStyleLinkNew.href = darkHref;
            mainStyleLinkNew.dataset.theme = 'dark';

            changeTheme.querySelector('.dark').classList.remove('d-none');
            changeTheme.querySelector('.light').classList.add('d-none');

            document.body.classList.add('dark');
            document.body.classList.remove('light');
        }

        setCookie('theme', mainStyleLinkNew.dataset.theme, 300);
    })
}

function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
