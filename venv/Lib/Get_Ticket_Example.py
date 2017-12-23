# coding=utf-8

import asyncio
import ccxt
import time
import _thread
import datetime

def getBinance():
    print("Start binance")
    #Define the currency symbol that will be fetched
    symbol = 'IOTA/BTC'
    #Create the exchange wanted
    exchange = ccxt.binance()
    #Load the markets for said exchange
    markets = exchange.load_markets()
    #Fetches the ticker
    ticker = exchange.fetch_ticker(symbol.upper())
    print(ticker)

    #Ticker returns a dict, [String][value] pairs
    #'high' is the String in the ticker that retreives the value high price
    price=ticker['high']
    print("%s: %s" % (symbol, price))
    return price
getBinance()