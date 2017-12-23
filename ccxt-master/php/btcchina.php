<?php

namespace ccxt;

class btcchina extends Exchange {

    public function describe () {
        return array_replace_recursive (parent::describe (), array (
            'id' => 'btcchina',
            'name' => 'BTCChina',
            'countries' => 'CN',
            'rateLimit' => 1500,
            'version' => 'v1',
            'hasCORS' => true,
            'urls' => array (
                'logo' => 'https://user-images.githubusercontent.com/1294454/27766368-465b3286-5ed6-11e7-9a11-0f6467e1d82b.jpg',
                'api' => array (
                    'plus' => 'https://plus-api.btcchina.com/market',
                    'public' => 'https://data.btcchina.com/data',
                    'private' => 'https://api.btcchina.com/api_trade_v1.php',
                ),
                'www' => 'https://www.btcchina.com',
                'doc' => 'https://www.btcchina.com/apidocs'
            ),
            'api' => array (
                'plus' => array (
                    'get' => array (
                        'orderbook',
                        'ticker',
                        'trade',
                    ),
                ),
                'public' => array (
                    'get' => array (
                        'historydata',
                        'orderbook',
                        'ticker',
                        'trades',
                    ),
                ),
                'private' => array (
                    'post' => array (
                        'BuyIcebergOrder',
                        'BuyOrder',
                        'BuyOrder2',
                        'BuyStopOrder',
                        'CancelIcebergOrder',
                        'CancelOrder',
                        'CancelStopOrder',
                        'GetAccountInfo',
                        'getArchivedOrder',
                        'getArchivedOrders',
                        'GetDeposits',
                        'GetIcebergOrder',
                        'GetIcebergOrders',
                        'GetMarketDepth',
                        'GetMarketDepth2',
                        'GetOrder',
                        'GetOrders',
                        'GetStopOrder',
                        'GetStopOrders',
                        'GetTransactions',
                        'GetWithdrawal',
                        'GetWithdrawals',
                        'RequestWithdrawal',
                        'SellIcebergOrder',
                        'SellOrder',
                        'SellOrder2',
                        'SellStopOrder',
                    ),
                ),
            ),
            'markets' => array (
                'BTC/CNY' => array ( 'id' => 'btccny', 'symbol' => 'BTC/CNY', 'base' => 'BTC', 'quote' => 'CNY', 'api' => 'public', 'plus' => false ),
                'LTC/CNY' => array ( 'id' => 'ltccny', 'symbol' => 'LTC/CNY', 'base' => 'LTC', 'quote' => 'CNY', 'api' => 'public', 'plus' => false ),
                'LTC/BTC' => array ( 'id' => 'ltcbtc', 'symbol' => 'LTC/BTC', 'base' => 'LTC', 'quote' => 'BTC', 'api' => 'public', 'plus' => false ),
                'BCH/CNY' => array ( 'id' => 'bcccny', 'symbol' => 'BCH/CNY', 'base' => 'BCH', 'quote' => 'CNY', 'api' => 'plus', 'plus' => true ),
                'ETH/CNY' => array ( 'id' => 'ethcny', 'symbol' => 'ETH/CNY', 'base' => 'ETH', 'quote' => 'CNY', 'api' => 'plus', 'plus' => true ),
            ),
        ));
    }

    public function fetch_markets () {
        $markets = $this->publicGetTicker (array (
            'market' => 'all',
        ));
        $result = array ();
        $keys = array_keys ($markets);
        for ($p = 0; $p < count ($keys); $p++) {
            $key = $keys[$p];
            $market = $markets[$key];
            $parts = explode ('_', $key);
            $id = $parts[1];
            $base = mb_substr ($id, 0, 3);
            $quote = mb_substr ($id, 3, 6);
            $base = strtoupper ($base);
            $quote = strtoupper ($quote);
            $symbol = $base . '/' . $quote;
            $result[] = array (
                'id' => $id,
                'symbol' => $symbol,
                'base' => $base,
                'quote' => $quote,
                'info' => $market,
            );
        }
        return $result;
    }

    public function fetch_balance ($params = array ()) {
        $this->load_markets();
        $response = $this->privatePostGetAccountInfo ();
        $balances = $response['result'];
        $result = array ( 'info' => $balances );
        $currencies = array_keys ($this->currencies);
        for ($i = 0; $i < count ($currencies); $i++) {
            $currency = $currencies[$i];
            $lowercase = strtolower ($currency);
            $account = $this->account ();
            if (is_array ($balances['balance']) && array_key_exists ($lowercase, $balances['balance']))
                $account['total'] = floatval ($balances['balance'][$lowercase]['amount']);
            if (is_array ($balances['frozen']) && array_key_exists ($lowercase, $balances['frozen']))
                $account['used'] = floatval ($balances['frozen'][$lowercase]['amount']);
            $account['free'] = $account['total'] - $account['used'];
            $result[$currency] = $account;
        }
        return $this->parse_balance($result);
    }

    public function create_market_request ($market) {
        $request = array ();
        $field = ($market['plus']) ? 'symbol' : 'market';
        $request[$field] = $market['id'];
        return $request;
    }

    public function fetch_order_book ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $method = $market['api'] . 'GetOrderbook';
        $request = $this->create_market_request ($market);
        $orderbook = $this->$method (array_merge ($request, $params));
        $timestamp = $orderbook['date'] * 1000;
        $result = $this->parse_order_book($orderbook, $timestamp);
        $result['asks'] = $this->sort_by($result['asks'], 0);
        return $result;
    }

    public function parse_ticker ($ticker, $market) {
        $timestamp = $ticker['date'] * 1000;
        return array (
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => floatval ($ticker['high']),
            'low' => floatval ($ticker['low']),
            'bid' => floatval ($ticker['buy']),
            'ask' => floatval ($ticker['sell']),
            'vwap' => floatval ($ticker['vwap']),
            'open' => floatval ($ticker['open']),
            'close' => floatval ($ticker['prev_close']),
            'first' => null,
            'last' => floatval ($ticker['last']),
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => floatval ($ticker['vol']),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function parse_ticker_plus ($ticker, $market) {
        $timestamp = $ticker['Timestamp'];
        $symbol = null;
        if ($market)
            $symbol = $market['symbol'];
        return array (
            'symbol' => $symbol,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'high' => floatval ($ticker['High']),
            'low' => floatval ($ticker['Low']),
            'bid' => floatval ($ticker['BidPrice']),
            'ask' => floatval ($ticker['AskPrice']),
            'vwap' => null,
            'open' => floatval ($ticker['Open']),
            'close' => floatval ($ticker['PrevCls']),
            'first' => null,
            'last' => floatval ($ticker['Last']),
            'change' => null,
            'percentage' => null,
            'average' => null,
            'baseVolume' => floatval ($ticker['Volume24H']),
            'quoteVolume' => null,
            'info' => $ticker,
        );
    }

    public function fetch_ticker ($symbol, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $method = $market['api'] . 'GetTicker';
        $request = $this->create_market_request ($market);
        $tickers = $this->$method (array_merge ($request, $params));
        $ticker = $tickers['ticker'];
        if ($market['plus'])
            return $this->parse_ticker_plus ($ticker, $market);
        return $this->parse_ticker($ticker, $market);
    }

    public function parse_trade ($trade, $market) {
        $timestamp = intval ($trade['date']) * 1000;
        return array (
            'id' => $trade['tid'],
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $market['symbol'],
            'type' => null,
            'side' => null,
            'price' => $trade['price'],
            'amount' => $trade['amount'],
        );
    }

    public function parse_trade_plus ($trade, $market) {
        $timestamp = $this->parse8601 ($trade['timestamp']);
        return array (
            'id' => null,
            'info' => $trade,
            'timestamp' => $timestamp,
            'datetime' => $this->iso8601 ($timestamp),
            'symbol' => $market['symbol'],
            'type' => null,
            'side' => strtolower ($trade['side']),
            'price' => $trade['price'],
            'amount' => $trade['size'],
        );
    }

    public function parse_trades_plus ($trades, $market = null) {
        $result = array ();
        for ($i = 0; $i < count ($trades); $i++) {
            $result[] = $this->parse_trade_plus ($trades[$i], $market);
        }
        return $result;
    }

    public function fetch_trades ($symbol, $since = null, $limit = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $method = $market['api'] . 'GetTrade';
        $request = $this->create_market_request ($market);
        if ($market['plus']) {
            $now = $this->milliseconds ();
            $request['start_time'] = $now - 86400 * 1000;
            $request['end_time'] = $now;
        } else {
            $method .= 's'; // trades vs trade
        }
        $response = $this->$method (array_merge ($request, $params));
        if ($market['plus']) {
            return $this->parse_trades_plus ($response['trades'], $market);
        }
        return $this->parse_trades($response, $market, $since, $limit);
    }

    public function create_order ($symbol, $type, $side, $amount, $price = null, $params = array ()) {
        $this->load_markets();
        $market = $this->market ($symbol);
        $method = 'privatePost' . $this->capitalize ($side) . 'Order2';
        $order = array ();
        $id = strtoupper ($market['id']);
        if ($type == 'market') {
            $order['params'] = array ( null, $amount, $id );
        } else {
            $order['params'] = array ( $price, $amount, $id );
        }
        $response = $this->$method (array_merge ($order, $params));
        return array (
            'info' => $response,
            'id' => $response['id'],
        );
    }

    public function cancel_order ($id, $symbol = null, $params = array ()) {
        $this->load_markets();
        $market = $params['market']; // TODO fixme
        return $this->privatePostCancelOrder (array_merge (array (
            'params' => array ( $id, $market ),
        ), $params));
    }

    public function nonce () {
        return $this->microseconds ();
    }

    public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
        $url = $this->urls['api'][$api] . '/' . $path;
        if ($api == 'private') {
            $this->check_required_credentials();
            $p = array ();
            if (is_array ($params) && array_key_exists ('params', $params))
                $p = $params['params'];
            $nonce = $this->nonce ();
            $request = array (
                'method' => $path,
                'id' => $nonce,
                'params' => $p,
            );
            $p = implode (',', $p);
            $body = $this->json ($request);
            $query = (
                'tonce=' . $nonce +
                '&accesskey=' . $this->apiKey +
                '&requestmethod=' . strtolower ($method) +
                '&id=' . $nonce +
                '&$method=' . $path +
                '&$params=' . $p
            );
            $signature = $this->hmac ($this->encode ($query), $this->encode ($this->secret), 'sha1');
            $auth = $this->encode ($this->apiKey . ':' . $signature);
            $headers = array (
                'Authorization' => 'Basic ' . base64_encode ($auth),
                'Json-Rpc-Tonce' => $nonce,
            );
        } else {
            if ($params)
                $url .= '?' . $this->urlencode ($params);
        }
        return array ( 'url' => $url, 'method' => $method, 'body' => $body, 'headers' => $headers );
    }
}