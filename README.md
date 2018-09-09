# new_warehouse
Описание запросов:


GET /api/v1/products - информация обо всех продуктах

POST /api/v1/products - добавить продукт

PUT /api/v1/products/{id} - изменить продукт

GET /api/v1/products/{id} - информация об одном продукте

DELETE /api/v1/products/{id} - удалить продукт


GET /api/v1/warehouses - информация обо всех складах

POST /api/v1/warehouses - добавить склад

PUT /api/v1/warehouses/{id} - изменить склад

GET /api/v1/warehouses/{id} - информация об одном складе

DELETE /api/v1/warehouses/{id} - удалить склад


PUT /api/v1/warehouses/6/receipt - получить продукты на склад

PUT /api/v1/warehouses/6/dispatch - отправить продукты со склада

PUT /api/v1/warehouses/6/movement - переместить на другой склад

