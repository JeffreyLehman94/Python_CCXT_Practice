import time
import ccxt


# Example of depth working correctly with poloniex
polo = 'XMR/BTC'
exchange = ccxt.poloniex()
markets = exchange.load_markets()
book1 = ccxt.poloniex().fetch_order_book(polo, {'depth': 2})
print (book1)

## Example of depth not working for bittrex
exchange = ccxt.bittrex()
markets = exchange.load_markets()
book = ccxt.bittrex().fetch_order_book(polo, {'depth': 2})
print (book)

## Gets the market price and spread
bid = book['bids'][0][0] if len (book['bids']) > 0 else None
ask = book['asks'][0][0] if len (book['asks']) > 0 else None
spread = (ask - bid) if (bid and ask) else None
print (exchange.id, 'market price', { 'bid': bid, 'ask': ask, 'spread': spread })