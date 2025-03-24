# 3.0.0 (2025-03-24)

## Added

- Add airtime purchase operation operations
- Rename getTransaction to listTransactions
- Add getTransactions to get transaction by ids and source

# 3.0.0 (2025-03-04)

## Added

- Add fundraising operations
- Add wallet operations
- Add refund transaction operation

## === BREAKING CHANGES ===

- Parameters for make_collect and make_deposit are not more passed as dict but as keyword arguments
- Remove security operations
- Change parameter ts(str) to date(datetime) in Transaction class

# 2.2.1 (2024-06-29)

- **feat**: You can now use MeSomb::$language to set the language of the API
- **feat**: add function Util::detectOperator to detect operator from a phone number
- **fix**: fixing some bugs

# 2.1.2 (2024-01-10)

- **feat**: Add missing importation in init.php

# 2.1.1 (2023-11-15)

- **fix**: Fix issue when throwing ServerException exception.

# 2.1 (2023-06-28)

## === BREAKING CHANGES ===

Only one parameter is now passed to makeDeposit and makeCollect. The parameter is a Map that will contain all details of your request.

# 2.0 (2023-06-28)

Remove dependencies of guzzlehttp/guzzle and now rely only on curl

# 1.2 (2023-02-11)

Fix bug on handling response with redirect not set