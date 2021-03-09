#Exbico API Underwriting PHP Client

Библиотека для работы с Exbico Underwriting API.

## Примеры использования

#### Запрос на получение кредитного рейтинга НБКИ
```
API_URL=https://test.app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php examples/credit-rating-request.php
```
Ответ:
```
Credit rating NBCH requested with ID: 5158827
```

#### Получение статуса отчета
```
API_URL=https://test.app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php examples/report-status.php {REQUEST_ID}
```
Ответ:
```
Credit rating NBCH requested with ID: 5158827
```

#### Сохранение отчета НБКИ
```
API_URL=https://test.app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php examples/credit-rating-nbch-get.php {REQUEST_ID}
```
Ответ:
```
PDF Report downloaded: /.../exbico-underwriting-api-client/examples/report_20210309181819.pdf
```


#### Получение кредитного рейтинга НБКИ (полный цикл)
```
API_URL=https://test.app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php examples/credit-rating.php
```
Ответ:
```
Credit rating NBCH requested with ID: 5158826
Waiting for status change.......
Start to download report
Report downloaded: /.../exbico-underwriting-api-client/examples/report_20210309175001.pdf
```