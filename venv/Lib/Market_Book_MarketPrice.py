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
    print({'bid': bid, 'ask': ask, 'spread': spread}, exchange.id, 'market price')
    return spread


while True:
    s1 = getMarketPrice("poloniex", 'XMR/BTC')
    s2 = getMarketPrice("bittrex", 'XMR/BTC')
    s3 = getMarketPrice("kraken", 'XMR/BTC')
    if (s1 != s2 or s2 != s3 or s1 != s3):
        if (s1 != s2 and s1 != s3 and s2 != s3):
            damnwhathappened += 1
            file.write("%s ---%s---All spreads inconsistant, damn what happened?\n" % (damnwhathappened, datetime.datetime.now()))
        elif (s1 != s2 and s1 != s3):
            poloniex += 1
            file.write("%s ---%s---Poloniex spread inconsistant.\n" % (poloniex, str(datetime.datetime.now())))
        elif (s2 != s1 and s2 != s3):
            bittrex += 1
            file.write("%s ---%s---Bittrex spread inconsistant.\n" % (bittrex, str(datetime.datetime.now())))
        elif (s3 != s2 and s2 != s3):
            kraken += 1
            file.write("%s ---%s---Kraken spread inconsistant.\n" % (kraken, str(datetime.datetime.now())))
        print("INCONSISTANT SPREADS")
        file.flush()
    print("\n")
    time.sleep(1)

# Example of depth working correctly with poloniex
symbol = 'XMR/BTC'
exchange1 = ccxt.poloniex()
markets1 = exchange1.load_markets()
book1 = ccxt.poloniex().fetch_order_book(symbol, {'depth': 2})
# print (book1)

## Example of depth not working for bittrex
exchange2 = ccxt.bittrex()
markets2 = exchange2.load_markets()
book2 = ccxt.bittrex().fetch_order_book(symbol, {'depth': 2})
# print (book2)
