# Services Overview — SRP Library System

## BookService

This is the richest service. Its job is to apply all business rules related to books before delegating storage to `BookRepository`.

**CRUD delegation** — `getAll`, `findById`, `create`, `update`, `delete` are thin wrappers that forward calls to the repository. The service only adds rules on top:

- `create` — validates that `availible_copies` is not negative before allowing creation. (Note: the key has a typo — `availible_copies` vs the DB column `available_copies`.)
- `update` — first calls `findById` to confirm the book exists (will throw a 404 via `findOrFail` if not), then re-validates copies if the field is present in the payload.

**Domain logic** — these three methods are the real reason this service exists:

- `isAvailable(int $id): bool` — fetches the book and returns `true` if `available_copies > 0`. Used by `LoanService` before creating a loan.
- `dreacreseCopies(int $id)` — fetches the book, guards against 0 copies, then decrements by 1. Called when a loan is processed.
- `increaseCopies(int $id)` — fetches the book and increments by 1. Called when a book is returned.

---

## MemberService

Simpler than `BookService`. Its only real business rule is email normalization.

- `create` — lowercases the email before passing data to the repository. Has a typo: it sets `$data['emal']` (missing the `i`) instead of `$data['email']`, so the normalization is silently lost.
- `update` — checks the member exists via `findById`, then lowercases the email only if it is present in the payload (safe partial update).
- `getAll` / `findById` — pure delegation, no business logic.

There is no `delete` method here even though `MemberRepository` has one, so deletion is not yet exposed through the service layer.

---

## LoanService

This is the orchestrator — it coordinates `LoanRepository` and `BookService` together to enforce the loan lifecycle.

```
__construct(LoanRepository, BookService)
```

- `create(array $data)` — the full loan flow in two steps:
  1. Calls `bookService->isAvailable($data['book_id'])` — if no copies, throws immediately.
  2. Persists the loan via `loanRepository->create($data)`.
  3. Calls `bookService->dreacreseCopies($data['book_id'])` to decrement stock.

- `returnLoan(int $loanId)` — the return flow:
  1. Fetches the loan to get the `book_id`.
  2. Calls `bookService->increaseCopies($loan->book_id)` to restore stock.
  3. Deletes the loan record (`loanRepository->delete($loanId)`). This means a return is treated as deletion rather than a status update — `returned_at` in the schema is never used.

---

## Suggested Small Improvements

| # | Where | Issue | Fix |
|---|---|---|---|
| 1 | `BookService::create` | Key typo `availible_copies` doesn't match the DB column `available_copies` | Rename to `available_copies` |
| 2 | `MemberService::create` | Typo `$data['emal']` — email normalization is silently discarded | Change to `$data['email']` |
| 3 | `BookService::dreacreseCopies` | Method name has a spelling error | Rename to `decreaseCopies` |
| 4 | `LoanService::returnLoan` | Deletes the loan instead of setting `returned_at` | Update with `returned_at = now()` so history is preserved |
| 5 | `MemberService` | No `delete` method despite the repository having one | Add `delete(int $id)` |
| 6 | All services | No return types declared (`getAll`, `findById`, `create`, `update`) | Add `: Collection`, `: Model`, etc. for IDE support and clarity |
