import time
import ccxt


# Example of depth working correctly with poloniex
symbol = 'XMR/BTC'
exchange1 = ccxt.poloniex()
markets1 = exchange1.load_markets()
book1 = ccxt.poloniex().fetch_order_book(symbol, {'depth': 2})
print (book1)

## Example of depth not working for bittrex
exchange2 = ccxt.bittrex()
markets2 = exchange2.load_markets()
book2 = ccxt.bittrex().fetch_order_book(symbol, {'depth': 2})
print (book2)

## Gets the market price and spread
bid1 = book1['bids'][0][0] if len (book1['bids']) > 0 else None
ask1 = book1['asks'][0][0] if len (book1['asks']) > 0 else None
spread1 = (ask1 - bid1) if (bid1 and ask1) else None
print (exchange1.id, 'market price', { 'bid': bid1, 'ask': ask1, 'spread': spread1 })

bid2 = book2['bids'][0][0] if len (book2['bids']) > 0 else None
ask2 = book2['asks'][0][0] if len (book2['asks']) > 0 else None
spread2 = (ask2 - bid2) if (bid2 and ask2) else None
print (exchange1.id, 'market price', { 'bid': bid2, 'ask': ask2, 'spread': spread2})