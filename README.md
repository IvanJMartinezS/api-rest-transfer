# API for Deposit, withdraw and transactions 

Following we have a sequence of requests that will be used to test the API.


## Reset state before starting tests

```
POST /reset

200 OK
```

## Get balance for existing account

```
GET /balance?account_id=1

200 10
```


## Get balance for non-existing account

```
GET /balance?account_id=1234

404 0
```

## Deposit into existing account

```
POST /event 
JSON {"type":"deposit", "destination":1, "amount":10}

201 {"destination": {"id":"100", "balance":10}}
```

## Deposit into non-existing account

```
POST /event 
JSON {"type":"deposit", "destination":100, "amount":10}

404 0
```


## Withdraw from existing account

```
POST /event 
JSON {"type":"withdraw", "origin":1, "amount":10}

201 {"origin": {"id":"100", "balance":15}}
```

## Withdraw from non-existing account

```
POST /event 
JSON {"type":"withdraw", "origin":100, "amount":10}

404 0
```

## Withdraw amount > balance

```
POST /event 
JSON {"type":"withdraw", "origin":100, "amount":10}

404 {"Error": {"Response": "Saldo Insuficiente"}, "origin": {"id": 2, "balance": 0}}
```

## Transfer from existing account

```
POST /event 
JSON {"type":"transfer", "origin":1, "amount":10, "destination":2}

201 {"origin": {"id":"100", "balance":0}, "destination": {"id":"300", "balance":15}}
```

## Transfer from non-existing account

```
POST /event 
JSON{"type":"transfer", "origin":100, "amount":10, "destination":200}

404 0
```
