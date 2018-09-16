# Backend summer school 2018 - итоговая работа

Многопользовательское приложение для управления складскими запасами. Сервис позволяет каждому пользователю завести учетную запись и в рамках нее вести управление складами и запасами товаров на складах. Реализовано как backend сервис работающий по протоколу HTTP и использующий стандарт RESTful API. Cтек - PHP, MySQL. Для аутентификации используется HTTP Basic Auth, где параметром будет пара логин-пароль. Все POST-, PUT- и DELETE-запросы должны быть в формате JSON. Все ответы также возвращаются в формате JSON.


## Описание методов API

### POST /api/v1/users
создает нового пользователя

#### Параметры
- login - имя (идентификатор) учётной записи
- password - пароль
- name - имя
- surname - фамилия
- organization - организация 
- email - адрес электронной почты
- phoneNumber - номер телефона

#### Ограничения
- двух человек с одинаковым именем в одной организации быть не может
- один email не может использоваться более чем одним пользователем

#### Тело запроса
```json
{
    "login": "Toma",
    "name": "Tamara",
    "surname": "Vedenina",
    "organization": "PSU",
    "email": "toma5@gmail.com",
    "phoneNumber": "88005553535"
}
```
#### Результат
после успешного выполнения возвращает созданный объект
```json
{
    "id": 7,
    "login": "Toma",
    "name": "Tamara",
    "surname": "Vedenina",
    "organization": "PSU",
    "email": "toma5@gmail.com",
    "phoneNumber": "88005553535"
}
```

### GET /api/v1/me
возвращает информацию о текущем пользователе

#### Результат
```json
{
    "id": 8,
    "login": "test",
    "name": "Petya",
    "surname": "Ivanov",
    "organization": "Xsolla",
    "email": "ivanov@gmail.com",
    "phoneNumber": "88005553535"
}
```

### PUT /api/v1/me 
позволяет обновить данные о текущем пользователе

#### Параметры
- login - имя (идентификатор) учётной записи
- password - пароль
- name - имя
- surname - фамилия
- organization - организация 
- email - адрес электронной почты
- phoneNumber - номер телефона

#### Ограничения
- двух человек с одинаковым именем в одной организации быть не может
- один email не может использоваться более чем одним пользователем

#### Тело запроса
может содержать один или несколько параметров
```json
{
    "organization": "Xsolla",
    "email": "ivanov.pa@gmail.com"
}
```
#### Результат
после успешного выполнения возвращает обновленный объект
```json
{
    "id": 8,
    "login": "test",
    "name": "Petya",
    "surname": "Ivanov",
    "organization": "Xsolla",
    "email": "ivanov.pa@gmail.com",
    "phoneNumber": "88005553535"
}
```

### DELETE /api/v1/me
удаляет учетную запись пользователя и все связанные с ней данные

#### Результат
после успешного выполнения ничего не возвращает

### GET /api/v1/products
возвращает список всех товаров пользователя в виде массива объектов

#### Результат
```json
[
    {
        "sku": 5255,
        "name": "Сыр Хохланд",
        "price": 500,
        "size": 10,
        "type": "food"
    },
    {
        "sku": 589,
        "name": "Каша овсяная \"Геркулес\"",
        "price": 50,
        "size": 1,
        "type": "food"
    }
]
```

### GET /api/v1/products/{sku}
возвращает информацию об одном товаре

#### Результат
```json
{
    "sku": 589,
    "name": "Каша овсяная \"Геркулес\"",
    "price": 50,
    "size": 1,
    "type": "food"
}
```

### POST /api/v1/products
создает новый товар

#### Параметры
- name - название товара
- sku - уникальный учетный номер
- price - цена
- size - размер в абстрактных еденицах
- type - тип

#### Ограничения
- товар имеет уникальный учетный номер в пределах одного пользователя

#### Тело запроса
```json
{
    "name": "Шоколадный батончик \"Bounty\"",
    "sku": 878,
    "price":  50,
    "size": 2,
    "type": "food"
}
```
#### Результат
после успешного выполнения возвращает созданный объект
```json
{
    "sku": 878,
    "name": "Шоколадный батончик \"Bounty\"",
    "price": 50,
    "size": 2,
    "type": "food"
}
```

### PUT /api/v1/products/{sku}
позволяет изменить информацию о товаре

#### Параметры
- name - название товара
- sku - уникальный учетный номер
- price - цена
- size - размер в абстрактных еденицах
- type - тип

#### Ограничения
- товар имеет уникальный учетный номер в пределах одного пользователя
- нельзя обновить размер товара, если он уже учитывается по складам

#### Тело запроса
может содержать один или несколько параметров
```json
{
    "price": 300
}
```
#### Результат
возвращает обновленный объект
```json
{
    "sku": 5255,
    "name": "Сыр Хохланд",
    "price": 300,
    "size": 10,
    "type": "food"
}
```

### DELETE /api/v1/products/{sku}
удаляет товар

#### Ограничения
- нельзя удалить товар, если он уже учитываются в перемещениях

#### Результат
после успешного выполнения ничего не возвращает

### GET /api/v1/warehouses
возвращает список всех складов

#### Результат
массив объектов
```json
[
    {
        "id": "24",
        "address": "kompros-69",
        "capacity": "50000"
    },
    {
        "id": "25",
        "address": "lenina-25",
        "capacity": "100000"
    }
]
```

### GET /api/v1/warehouses/{id} 
возвращает информацию об одном складе

#### Результат
объект
```json
{
    "id": 24,
    "address": "kompros-69",
    "capacity": 50000
}
```
### POST /api/v1/warehouses
создает новый склад

#### Параметры
- address - адресс склада
- capacity - емкость в абстрактных единицах

#### Ограничения
- склад имеет уникальный учетный адрес в пределах одного пользователя

#### Тело запроса
```json
{
    "address": "lenina-25", 
    "capacity": 100000
}
```
#### Результат
созданный объект
```json
{
    "id": 25,
    "address": "lenina-25",
    "capacity": 100000
}
```

### PUT /api/v1/warehouses/{id} 
позволяет изменить информацию о складе

#### Параметры
- address - адресс склада
- capacity - емкость в абстрактных единицах

#### Ограничения
- склад имеет уникальный учетный адрес в пределах одного пользователя
- емкость склада не может быть меньше уже занятого на нем места

#### Тело запроса
может содержать один или несколько параметров
```json
{
    "capacity": 200000
}
```
#### Результат
обновленный объект
```json
{
    "id": 25,
    "address": "lenina-25",
    "capacity": 200000
}
```

### DELETE /api/v1/warehouses/{id} 
удаляет склад

#### Ограничения
- нельзя удалить склад, если по нему уже учитываются перемещения

#### Результат
после успешного выполнения ничего не возвращает

### PUT /api/v1/warehouses/{id}/receipt
принять партию товаров на склад

#### Параметры
- sku - учетный номер товара
- quantity - количеств
- sender - ФИО отправителя

#### Ограничения
- склад не может принять товаров больше, чем в него влазит

#### Тело запроса
```json
[
    {
        "sku": 5255,
	"sender": "Иванов Иван Иванович",
	"quantity": 500
    },
    {
	"sku": 589,
	"sender": "Петров Иван Иванович",
	"quantity": 500
    }
]
```
#### Результат
информация о перемещениях в виде массива объктов
```json
[
    {
        "transactionId": 82,
        "sku": 5255,
        "quantity": 500,
        "direction": "receipt",
        "datetime": "2018-09-16 15:15:14",
        "sender": "Иванов Иван Иванович",
        "recipient": 25
    },
    {
        "transactionId": 83,
        "sku": 589,
        "quantity": 500,
        "direction": "receipt",
        "datetime": "2018-09-16 15:15:14",
        "sender": "Петров Иван Иванович",
        "recipient": 25
    }
]
```

### PUT /api/v1/warehouses/{id}/dispatch
отправить партию товаров со склада

#### Параметры
- sku - учетный номер товара
- quantity - количество
- recipient - ФИО получателя

#### Тело запроса
```json
[
    {
	"sku": 5255,
	"recipient": "Петров Иван Иванович",
	"quantity": 100
    },
    {
	"sku": 589,
	"recipient": "Пазолини Корней Свястоплясов",
	"quantity": 1000
    },
    {
	"sku": 5255,
	"recipient": "Грозный Вахтанг Петрович",
	"quantity": 500
    }
]
```
#### Результат
информация о перемещениях в виде массива объктов
```json
[
    {
        "transactionId": 86,
        "sku": 5255,
        "quantity": 100,
        "direction": "dispatch",
        "datetime": "2018-09-16 15:30:17",
        "sender": 25,
        "recipient": "Петров Иван Иванович"
    },
    {
        "transactionId": 87,
        "sku": 589,
        "quantity": 1000,
        "direction": "dispatch",
        "datetime": "2018-09-16 15:30:17",
        "sender": 25,
        "recipient": "Пазолини Корней Свястоплясов"
    },
    {
        "transactionId": 88,
        "sku": 5255,
        "quantity": 500,
        "direction": "dispatch",
        "datetime": "2018-09-16 15:30:17",
        "sender": 25,
        "recipient": "Грозный Вахтанг Петрович"
    }
]
```

### PUT /api/v1/warehouses/{id}/movement 
переместить партию товаров на другой склад

#### Параметры
- sku - учетный номер товара
- quantity - количество
- warehouseId - учетный номер склада, на который будет перемещаться товар

#### Ограничения: 
- пользователь может перемещать только свои товары на своих складах

#### Тело запроса
```json
[
    {
        "sku": 589,
	"warehouseId": 24,
	"quantity": 10
    },
    {
	"sku": 5255,
	"warehouseId": 26,
	"quantity": 20
    }
]
```
#### Результат
информация о перемещениях в виде массива объктов
```json
[
    {
        "transactionId": 91,
        "sku": 589,
        "quantity": 10,
        "direction": "betweenWarehouses",
        "datetime": "2018-09-16 15:36:00",
        "sender": 25,
        "recipient": 24
    },
    {
        "transactionId": 92,
        "sku": 5255,
        "quantity": 20,
        "direction": "betweenWarehouses",
        "datetime": "2018-09-16 15:36:00",
        "sender": 25,
        "recipient": 26
    }
]
```

### GET /api/v1/warehouses/{id}/residues
возвращает текущее состояние по остаткам на конкретном складе в количестве и общей стоимости всех товаров

### GET /api/v1/warehouses/{id}/residues/{date} - получить на конкретную дату состояние по остаткам на конкретном складе по количеству и общей стоимости товаров (формат даты 'Y-m-d')


### GET /api/v1/products/{sku}/residues - получить текущее состояние остатков по товару по всем складам  в количестве и общей стоимости

### GET /api/v1/products/{sku}/residues/{date} - получить на конкретную дату состояние остатков по товару по всем складам и общей стоимости товаров (формат даты 'Y-m-d')

### GET /api/v1/warehouses/{id}/movements - получить все движения товаров по конкретному складу

### GET /api/v1/products/{sku}/movements - получить все движения конкретного товара по складам с учетом сумм и остатков

