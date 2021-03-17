# Примеры использования

#### Запрос на получение кредитного рейтинга НБКИ (*credit-rating-nbch-request.php*)
```
API_URL=https://test.app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php credit-rating-request.php
```
Ответ:
```
Credit rating NBCH requested with ID: 5158827
```

#### Получение статуса отчета (*report-status.php*)
```
API_URL=https://test.app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php report-status.php {REQUEST_ID}
```
Ответ:
```
Report status: success
```

#### Получение отчета НБКИ (*credit-rating-nbch-download.php*)
```
API_URL=https://test.app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php credit-rating-nbch-download.php {REQUEST_ID}
```
Ответ:
```
PDF Report downloaded: /.../exbico-underwriting-api-client/examples/report_20210309181819.pdf
```

#### Получение кредитного рейтинга НБКИ - полный цикл (*credit-rating-nbch.php*)
```
API_URL=https://test.app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php credit-rating-nbch.php
```
Ответ:
```
Credit rating NBCH requested with ID: 5158826
Waiting for status change.......
Start to download report
Report downloaded: /.../exbico-underwriting-api-client/examples/report_20210309175001.pdf
```