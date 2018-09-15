# new_warehouse
Описание запросов:

GET /api/v1/products - информация обо всех продуктах

POST /api/v1/products - добавить продукт

PUT /api/v1/products/{sku} - изменить продукт

GET /api/v1/products/{sku} - информация об одном продукте

DELETE /api/v1/products/{sku} - удалить продукт


GET /api/v1/warehouses - информация обо всех складах

POST /api/v1/warehouses - добавить склад

PUT /api/v1/warehouses/{id} - изменить склад

GET /api/v1/warehouses/{id} - информация об одном складе

DELETE /api/v1/warehouses/{id} - удалить склад


PUT /api/v1/warehouses/6/receipt - получить продукты на склад

PUT /api/v1/warehouses/6/dispatch - отправить продукты со склада

PUT /api/v1/warehouses/6/movement - переместить на другой склад

GET /api/v1/warehouses/{id}/residues - получить текущее состояние по остаткам на конкретном складе в количестве и общей стоимости всех товаров

GET /api/v1/warehouses/{id}/residues/{date} - получить на конкретную дату состояние по остаткам на конкретном складе по количеству и общей стоимости товаров (формат даты 'Y-m-d')


GET /api/v1/products/{sku}/residues - получить текущее состояние остатков по товару по всем складам  в количестве и общей стоимости

GET /api/v1/products/{sku}/residues/{date} - получить на конкретную дату состояние остатков по товару по всем складам и общей стоимости товаров (формат даты 'Y-m-d')

GET /api/v1/warehouses/{id}/movements - получить все движения товаров по конкретному складу

GET /api/v1/products/{sku}/movements - получить все движения конкретного товара по складам с учетом сумм и остатков

POST /api/v1/users - добавить пользователя

GET /api/v1/me - посмотреть информацию о текущем пользователе

PUT /api/v1/me - обновить данные о текущем пользователе

DELETE /api/v1/me - удалить учетную запись и все связанные с ней данные