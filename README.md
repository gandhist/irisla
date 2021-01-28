# Iris Midtrans Library FOR LARAVEL PROJECTS
this library is to make a payout using iris midtrans with laravel

## REQUIREMENTS
- laravel min 5.7
- PHP version 7.2
- env = IRIS_END_POINT=https://app.sandbox.midtrans.com/iris/api/v1/
- env = IRIS_API_KEY_CREATOR={YOUR-IRIS-API-CREATOR}
- env = IRIS_API_KEY_APPROVER={YOUR-IRIS-API-APPROVER}

## HOW TO USE
- install
composer require gandhist/irisla
- import library
use Gandhist\Irisla\Beneficiaries;
use Gandhist\Irisla\Payouts;
use Gandhist\Irisla\Transactions;

- create new instance
$my_balance = new Transactions->balance_aggregator();

  
## current function
all method needs url end point and body from official iris api, reference :  **[IRIS Docs](https://iris-docs.midtrans.com/#iris-api)**
# Beneficiaries
- get(null)
- post($body)
- patch($alias_name, $body)
# Payouts
- create($body)
- approve($body)
- reject($body)
- details($reference_no)
# Transactions
- history($body) // from_date, to_date
- top_up_channel()
- balance_aggregator()
- bank_accounts()
- balance_facilitator()
