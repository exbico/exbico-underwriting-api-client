# Примеры использования

#### Запрос на получение кредитного рейтинга НБКИ (*credit-rating-nbch-request.php*)
Mac \ Linux:
```
API_URL=https://app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php credit-rating-request.php {LEAD_ID}
```
Windows:
```
set API_URL=https://app.exbico.ru/underwritingApi && set API_TOKEN={EXBICO_API_TOKEN} && php credit-rating-request.php {LEAD_ID}
```
Ответ:
```
Credit rating NBCH requested with ID: 5158827
Lead credit rating NBCH requested with ID: 5158828
```

#### Получение статуса отчета (*report-status.php*)
Mac \ Linux
```
API_URL=https://app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php report-status.php {REQUEST_ID}
```
Windows
```
set API_URL=https://app.exbico.ru/underwritingApi && set API_TOKEN={EXBICO_API_TOKEN} && php report-status.php {REQUEST_ID}
```
Ответ:
```
Report status: success
```

#### Получение цены отчета НБКИ (*report-price-nbch.php*)
Mac \ Linux:
```
API_URL=https://app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php report-price-nbch.php {LEAD_ID}
```
Windows:
```
set API_URL=https://app.exbico.ru/underwritingApi && set API_TOKEN={EXBICO_API_TOKEN} && php report-price-nbch.php {LEAD_ID}
```
Ответ:
```
Report price: 40
Report price for lead: 40
```

#### Получение цены продукта "Скоринг" (*report-price-scoring.php*)
Mac \ Linux:
```
API_URL=https://app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php report-price-scoring.php {LEAD_ID}
```
Windows:
```
set API_URL=https://app.exbico.ru/underwritingApi && set API_TOKEN={EXBICO_API_TOKEN} && php report-price-scoring.php {LEAD_ID}
```
Ответ:
```
Report price: 0
```

#### Получение отчета НБКИ (*credit-rating-nbch-download.php*)
Mac \ Linux:
```
API_URL=https://app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php credit-rating-nbch-download.php {REQUEST_ID}
```
Windows:
```
set API_URL=https://app.exbico.ru/underwritingApi && set API_TOKEN={EXBICO_API_TOKEN} && php credit-rating-nbch-download.php {REQUEST_ID}
```
Ответ:
```
PDF Report downloaded: /.../exbico-underwriting-api-client/examples/report_20210309181819.pdf
```

#### Получение кредитного рейтинга НБКИ &dash; полный цикл (*credit-rating-nbch.php*)
Mac \ Linux:
```
API_URL=https://app.exbico.ru/underwritingApi API_TOKEN={EXBICO_API_TOKEN} php credit-rating-nbch.php
```
Windows:
```
set API_URL=https://app.exbico.ru/underwritingApi && set API_TOKEN={EXBICO_API_TOKEN} && php credit-rating-nbch.php
```
Ответ:
```
Credit rating NBCH requested with ID: 5158826
Waiting for status change.......
Start to download report
Report downloaded: /.../exbico-underwriting-api-client/examples/report_20210309175001.pdf
```