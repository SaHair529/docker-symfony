Ссылка на эту же документацию в гугл докс - https://docs.google.com/document/d/16kgJnMerm08Wdc7SU4yCUv0SoNS7LhxB8CTdPsZHAok/edit


Не забудьте добавить `symfony.localhost` в ваш `/etc/hosts` файл перед запуском docker-compose !!!!

Вывод списка книг (GET):
 -symfony.localhost:85/book/list?authors=<ФИО авторов через запятую>&genres=<жанры через запятую>&dates=<диапазон дат в формате d.m.Y через тире>
Пример запроса: 
-symfony.localhost:85/book/list?authors=Пушкин,Ремарк&genres=триллер,драма&dates=11.08.1998-11.08.2023

Если get-параметры пусты, выводятся все книги. Количество параметров не расширяют а сужают количество сделок. То есть, если по автору книга существует, а по жанру - нет, ничего выведено не будет.

Вывод информации по книге (GET):
 -symfony.localhost:85/book/<id книги>
Пример запроса:
 -symfony.localhost:85/book/1

Добавление книги (POST):
-symfony.localhost:85/book/create
Параметры:
title - название(строка)
description - описание(строка)
written_at - время написaния(дата в формате d.m.Y)
authors - список авторов (массив с ФИО авторов)
genres - список жанров (массив с наименованиями жанров)
Пример запроса:
symfony.localhost:85/book/create
request_data:
{
   "title": "Три Товарища",
   "description": "Книга о трёх друзьях",
   "written_at": "11.08.1998",
   "authors": ["Эрих Мария Ремарк"],
   "genres": ["Драма", "Трагедия", "Проза"]
}
Если на момент добавления автора или жанра, их не существует, они будут созданы и добавлены к книге (этот момент в ТЗ не обговаривается, поэтому, я решил сделать по-своему)

Изменение книги (POST):
 -symfony.localhost:85/book/<id>/update
Параметры:
title - название(строка)
description - описание(строка)
written_at - время написaния(дата в формате d.m.Y)
Пример запроса:

symfony.localhost:85/book/1/update
request data:
{
   "title": ”Название",
   "written_at": "11.08.1998",
   "description": "Описание"
}




Вывод списка авторов (GET):
 -symfony.localhost:85/author/list


Добавление автора(POST):
 -symfony.localhost:85/author/create
Параметры:
fullname - ФИО (строка)
Пример запроса:
 -symfony.localhost:85/author/create
request data:
{
   “fullname”: “Пушкин”
}


Обновление автора(POST):
 -symfony.localhost:85/author/<id>/update
Параметры:
fullname - ФИО (строка)
Пример запроса:
 -symfony.localhost:85/author/1/update
request data:
{
   “fullname”: “Пушкин”
}


Удаление автора(GET):
 -symfony.localhost:85/author/<id>/delete
Пример запроса:
 -symfony.localhost:85/author/1/delete







