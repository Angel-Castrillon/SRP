# Dificultad Extra — Sistema de Gestión de Biblioteca (SRP)

Stack: **Laravel + Angular + PostgreSQL**

---

## Base de datos (PostgreSQL)

### 1. Crear la base de datos
```sql
CREATE DATABASE srp_practice;
```

### 2. Migraciones en Laravel
```bash
php artisan make:migration create_books_table
php artisan make:migration create_members_table
php artisan make:migration create_loans_table
```

Campos por tabla:

**books**
| Campo | Tipo |
|---|---|
| id | bigIncrements |
| title | string |
| author | string |
| available_copies | integer |
| timestamps | timestamps |

**members**
| Campo | Tipo |
|---|---|
| id | bigIncrements |
| name | string |
| identification | string (unique) |
| email | string (unique) |
| timestamps | timestamps |

**loans**
| Campo | Tipo |
|---|---|
| id | bigIncrements |
| book_id | foreignId → books |
| member_id | foreignId → members |
| borrowed_at | timestamp |
| returned_at | timestamp (nullable) |
| timestamps | timestamps |

```bash
php artisan migrate
```

---

## Backend (Laravel)

### 3. Modelos
```bash
php artisan make:model Book
php artisan make:model Member
php artisan make:model Loan
```
- Define `$fillable` en cada modelo.
- En `Loan`: agrega las relaciones `belongsTo` con `Book` y `Member`.

### 4. Repositories (acceso a datos — SRP)
Crea `app/Repositories/` con:
- `BookRepository.php` — consultas CRUD de libros
- `MemberRepository.php` — consultas CRUD de miembros
- `LoanRepository.php` — consultas de préstamos y devoluciones

Cada repository tiene **una sola responsabilidad**: hablar con la base de datos de su entidad.

### 5. Services (lógica de negocio — SRP)
Crea `app/Services/` con:
- `BookService.php` — reglas de negocio de libros (ej. verificar copias disponibles)
- `MemberService.php` — reglas de negocio de miembros
- `LoanService.php` — procesar préstamo (descuenta copia) y devolución (suma copia)

Cada service tiene **una sola responsabilidad**: aplicar las reglas de su dominio.

### 6. Controllers (HTTP — SRP)
```bash
php artisan make:controller BookController --api
php artisan make:controller MemberController --api
php artisan make:controller LoanController --api
```
Cada controller tiene **una sola responsabilidad**: recibir la petición HTTP y devolver la respuesta.

### 7. Rutas API
En `routes/api.php`:
```php
Route::apiResource('books', BookController::class);
Route::apiResource('members', MemberController::class);
Route::apiResource('loans', LoanController::class);
Route::patch('loans/{loan}/return', [LoanController::class, 'return']);
```

### 8. CORS
En `config/cors.php`, permitir origen `http://localhost:4200`.

### 9. Iniciar el servidor
```bash
php artisan serve
```

---

## Frontend (Angular)

### 10. Interfaces
Crea `src/app/interfaces/`:
- `book.interface.ts`
- `member.interface.ts`
- `loan.interface.ts`

### 11. Services (comunicación HTTP — SRP)
```bash
ng generate service services/book
ng generate service services/member
ng generate service services/loan
```
Cada service usa `HttpClient` y tiene **una sola responsabilidad**: comunicarse con su endpoint.

### 12. Módulos por entidad (SRP)
Crea un módulo por cada entidad para aislar responsabilidades visuales:
```bash
ng generate module modules/books --routing
ng generate module modules/members --routing
ng generate module modules/loans --routing
```

### 13. Componentes
Dentro de cada módulo genera:
```bash
# Libros
ng generate component modules/books/pages/book-list
ng generate component modules/books/pages/book-form

# Miembros
ng generate component modules/members/pages/member-list
ng generate component modules/members/pages/member-form

# Préstamos
ng generate component modules/loans/pages/loan-list
ng generate component modules/loans/pages/loan-form
```

Cada componente tiene **una sola responsabilidad** visual.

### 14. Routing
En `app-routing-module.ts` usa lazy loading:
```ts
{ path: 'books',   loadChildren: () => import('./modules/books/books.module').then(m => m.BooksModule) },
{ path: 'members', loadChildren: () => import('./modules/members/members.module').then(m => m.MembersModule) },
{ path: 'loans',   loadChildren: () => import('./modules/loans/loans.module').then(m => m.LoansModule) },
```

### 15. HttpClientModule
Importa `HttpClientModule` en `AppModule`.

### 16. Iniciar el servidor
```bash
ng serve
```

---

## Verificación final

| Capa | Responsabilidad única |
|---|---|
| Repository | Solo accede a la base de datos |
| Service | Solo aplica reglas de negocio |
| Controller | Solo maneja HTTP |
| Angular Service | Solo hace llamadas HTTP |
| Componente | Solo gestiona su vista |

Accede a `http://localhost:4200` y prueba el flujo completo:
1. Registrar un libro
2. Registrar un miembro
3. Procesar un préstamo
4. Devolver el libro
