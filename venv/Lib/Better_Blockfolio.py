import ccxt
import smtplib
import time
import datetime

# This is a simple application which will check the current price
# of cryptocurrencies I hold in ETH, then into USD
# It then texts the total to my phone using email

DBC = 1378.3413789 # ON KUCOIN
BAT = 69.93 # ON BINANCE
COSS = 48.63344267 # ON COSS.IO

DBCbal = 0
BATbal = 0
COSSbal = 0
EtherUSD = 0
cont = 0




def kuCoin():
    symbol = 'DBC/ETH'
    exchange = ccxt.kucoin()
    markets = exchange.load_markets()
    ticker = exchange.fetch_ticker(symbol.upper())
    print(ticker)
    avgPrice = (ticker['bid']+ticker['ask'])/2
    print(avgPrice)
    return avgPrice
def Binance():
    symbol = 'BAT/ETH'
    exchange = ccxt.binance()
    markets = exchange.load_markets()
    ticker = exchange.fetch_ticker(symbol.upper())
    print(ticker)
    avgPrice = (ticker['bid']+ticker['ask'])/2
    print(avgPrice)
    return avgPrice
def HitBTC():
    symbol = 'COSS/ETH'
    exchange = ccxt.hitbtc()
    markets = exchange.load_markets()
    ticker = exchange.fetch_ticker(symbol.upper())
    print(ticker)
    avgPrice = (ticker['bid']+ticker['ask'])/2
    print(avgPrice)
    return avgPrice

def getEtherUSD():
    symbol = 'ETH/USD'
    exchange = ccxt.gdax()
    markets = exchange.load_markets()
    ticker = exchange.fetch_ticker(symbol.upper())
    print(ticker)
    avgPrice = (ticker['bid'] + ticker['ask']) / 2
    print(avgPrice)
    return avgPrice

def sendEmail(message, phone):
    smtpObj = smtplib.SMTP('smtp.gmail.com', 587)
    smtpObj.ehlo()
    smtpObj.starttls()
    smtpObj.login('jeffreylehman94@gmail.com', "Jeffreylehman1994")
    smtpObj.sendmail('jeffreylehman94@gmail.com', phone, message)

while True:
    cont = 1
    while cont==1:
        try:
            DBCbal=kuCoin()*DBC
            BATbal=Binance()*BAT
            COSSbal=HitBTC()*COSS
            EtherUSD=getEtherUSD()
            total = EtherUSD *(DBCbal+COSSbal+BATbal)
            print(total)
            sendEmail(str(total), '6094206366@txt.att.net')
            cont = 0
            time.sleep(60*15)
        except:
            continue