# Proposed Code Structure

```text
app/
  Domain/
    Catalog/
    Checkout/
    Orders/
    Accounts/
    Support/
    LslBridge/
  Http/
    Controllers/
      Storefront/
      Account/
      Admin/
      Api/
    Middleware/
    Requests/
  Models/
  Policies/
routes/
  web.php
  api.php
  admin.php
database/
  migrations/
  seeders/
resources/
  views/
    storefront/
    account/
    admin/
lsl/
  vendors/
  docs/
docs/
```

## Domain Responsibility Notes

- Keep business logic in `Domain/*` service/action classes.
- Keep controllers thin and request-validated.
- Use events + listeners for payment status transitions and delivery notifications.
- Use dedicated DTOs for LSL payload normalization.
