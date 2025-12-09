Inventory-App/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── ItemController.php
│   │   │   │   ├── UserController.php
│   │   │   │   ├── WorkInstructionController.php
│   │   │   │   ├── ItemLocationController.php
│   │   │   │   ├── StockController.php
│   │   │   │   └── ReportController.php
│   │   │   ├── User/
│   │   │   │   ├── WorkInstructionController.php
│   │   │   │   ├── ChecklistController.php
│   │   │   │   └── ReportController.php
│   │   │   └── Auth/
│   │   │       └── AuthController.php
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware.php
│   │   │   └── UserMiddleware.php
│   │   └── Requests/
│   │       ├── Admin/
│   │       │   ├── CreateWorkInstructionRequest.php
│   │       │   ├── UpdateItemRequest.php
│   │       │   └── TransferItemRequest.php
│   │       └── User/
│   │           ├── ChecklistRequest.php
│   │           └── ReportRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Item.php
│   │   ├── WorkInstruction.php
│   │   ├── ItemLocation.php
│   │   ├── StockMovement.php
│   │   ├── Checklist.php
│   │   └── Report.php
│   ├── ViewModels/
│   │   ├── Admin/
│   │   │   ├── WorkInstructionViewModel.php
│   │   │   ├── ItemManagementViewModel.php
│   │   │   └── ReportViewModel.php
│   │   └── User/
│   │       ├── WorkInstructionViewModel.php
│   │       └── ChecklistViewModel.php
│   ├── Services/
│   │   ├── Admin/
│   │   │   ├── ItemService.php
│   │   │   ├── WorkInstructionService.php
│   │   │   ├── StockService.php
│   │   │   └── ReportService.php
│   │   ├── User/
│   │   │   ├── WorkInstructionService.php
│   │   │   ├── ChecklistService.php
│   │   │   └── ReportService.php
│   │   └── Common/
│   │       ├── NotificationService.php
│   │       └── ValidationService.php
│   ├── Repositories/
│   │   ├── Contracts/
│   │   │   ├── ItemRepositoryInterface.php
│   │   │   ├── WorkInstructionRepositoryInterface.php
│   │   │   ├── StockRepositoryInterface.php
│   │   │   └── ReportRepositoryInterface.php
│   │   ├── Eloquent/
│   │   │   ├── ItemRepository.php
│   │   │   ├── WorkInstructionRepository.php
│   │   │   ├── StockRepository.php
│   │   │   └── ReportRepository.php
│   │   └── BaseRepository.php
│   ├── Enums/
│   │   ├── WorkInstructionType.php
│   │   ├── ItemCondition.php
│   │   ├── ChecklistStatus.php
│   │   └── UserRole.php
│   ├── DTOs/
│   │   ├── WorkInstructionDTO.php
│   │   ├── ChecklistDTO.php
│   │   └── ReportDTO.php
│   └── Exceptions/
│       ├── WorkInstructionException.php
│       ├── StockException.php
│       └── ValidationException.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_users_table.php
│   │   ├── 2024_01_01_000002_create_items_table.php
│   │   ├── 2024_01_01_000003_create_item_locations_table.php
│   │   ├── 2024_01_01_000004_create_work_instructions_table.php
│   │   ├── 2024_01_01_000005_create_work_instruction_items_table.php
│   │   ├── 2024_01_01_000006_create_stock_movements_table.php
│   │   ├── 2024_01_01_000007_create_checklists_table.php
│   │   └── 2024_01_01_000008_create_reports_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── UserSeeder.php
│   │   ├── ItemSeeder.php
│   │   └── ItemLocationSeeder.php
│   └── factories/
│       ├── UserFactory.php
│       ├── ItemFactory.php
│       └── WorkInstructionFactory.php
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── app.blade.php
│   │   │   ├── admin.blade.php
│   │   │   └── user.blade.php
│   │   ├── admin/
│   │   │   ├── items/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   ├── edit.blade.php
│   │   │   │   └── show.blade.php
│   │   │   ├── work-instructions/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── create.blade.php
│   │   │   │   ├── edit.blade.php
│   │   │   │   └── show.blade.php
│   │   │   ├── reports/
│   │   │   │   ├── stock.blade.php
│   │   │   │   ├── movement.blade.php
│   │   │   │   ├── low-stock.blade.php
│   │   │   │   └── wi-completion.blade.php
│   │   │   └── dashboard.blade.php
│   │   ├── user/
│   │   │   ├── work-instructions/
│   │   │   │   ├── index.blade.php
│   │   │   │   ├── show.blade.php
│   │   │   │   └── checklist.blade.php
│   │   │   ├── reports/
│   │   │   │   └── create.blade.php
│   │   │   └── dashboard.blade.php
│   │   └── auth/
│   │       ├── login.blade.php
│   │       └── register.blade.php
│   ├── js/
│   │   ├── admin/
│   │   │   ├── work-instruction.js
│   │   │   ├── item-management.js
│   │   │   └── reports.js
│   │   └── user/
│   │       ├── checklist.js
│   │       └── work-instruction.js
│   └── css/
│       ├── admin.css
│       ├── user.css
│       └── app.css
├── routes/
│   ├── web.php
│   ├── admin.php
│   └── user.php
├── config/
│   ├── inventory.php
│   └── permissions.php
└── tests/
    ├── Feature/
    │   ├── Admin/
    │   │   ├── ItemTest.php
    │   │   ├── WorkInstructionTest.php
    │   │   └── ReportTest.php
    │   └── User/
    │       ├── ChecklistTest.php
    │       └── WorkInstructionTest.php
    └── Unit/
        ├── Services/
        └── Repositories/