import time
import ccxt
import datetime

file = open("abc", "w")
poloniex = 0
bittrex = 0
kraken = 0
damnwhathappened = 0


def getMarketPrice(EXCHANGE, SYMBOL):
    if EXCHANGE == "poloniex":
        exchange = ccxt.poloniex()
    elif EXCHANGE == "bittrex":
        exchange = ccxt.bittrex()
    elif EXCHANGE == "kraken":
        exchange = ccxt.kraken()

    markets = exchange.load_markets()
    book = ccxt.poloniex().fetch_order_book(SYMBOL, {'depth': 10})
    # print(book)
    bid = book['bids'][0][0] if len(book['bids']) > 0 else None
    ask = book['asks'][0][0] if len(book['asks']) > 0 else None
    spread = (ask - bid) if (bid and ask) else None
    bid_ask = [bid, ask]
    #print(bid_ask)
    #print({'bid': bid, 'ask': ask, 'spread': spread}, exchange.id, 'market price')
    return bid_ask


while True:
    bittrex = getMarketPrice("bittrex", 'XMR/USDT')
    poloniex = getMarketPrice("poloniex", 'XMR/USDT')
    print("BITTREX : %s" %(bittrex))
    print("POLONIEX: %s" % (poloniex))
    if bittrex[0]!=poloniex[0] or bittrex[1]!=poloniex[1]:
        print("Arbitrage oppertunity")
    time.sleep(15)