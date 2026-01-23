Build a Bank Account Management System using Laravel 12 (latest) and Livewire. The application is designed to manage sensitive customer data, multiple bank accounts, and ATM cards under an agent-referral system.


erDiagram
    CUSTOMERS ||--o{ ACCOUNTS : "owns"
    CUSTOMERS ||--o{ COMPLAINTS : "reports"
    ACCOUNTS ||--o{ CARDS : "has"
    AGENTS ||--o{ COMPLAINTS : "handles"
    AGENTS ||--o{ ACCOUNTS : "referred_by"

    CUSTOMERS {
        uuid id PK
        string nik "Unique"
        string full_name
        string mother_name
        string email
        string phone_number
        text address
        string upload_ktp "File path"
        text note
        timestamp created_at
        timestamp updated_at
    }

    ACCOUNTS {
        uuid id PK
        uuid customer_id FK
        uuid agent_id FK
        string bank_name
        string branch
        string account_number "Unique"
        date opening_date
        text note
        enum status "aktif, bermasalah, nonaktif"
        timestamp updated_at
    }

    CARDS {
        uuid id PK
        uuid account_id FK
        string card_number "Unique"
        string cvv
        date expiry_date
        string pin_hash "Encrypted"
        string card_type
    }

    AGENTS {
        uuid id PK
        string agent_code "Unique"
        string agent_name
        string usertelegram
        string password "Hashed"
    }

    COMPLAINTS {
        uuid id PK
        uuid customer_id FK
        uuid agent_id FK
        string subject
        text description
        enum status "pending, processing, resolved"
        timestamp created_at
    }

Technical Specifications & Requirements
1. Model & Migration Guidelines
Use UUID as the Primary Key for all tables.

All timestamps (created_at, updated_at) must be included.
Storage: upload_ktp should store a relative path. Use Laravel's Storage facade.

2. Backend Framework
Framework: Laravel 12.

Admin Panel: Filament PHP v4 (for rapid CRUD development).

Authentication: Default Laravel Auth for Agents (use AGENTS table as the provider if possible

3. Core Features to Generate
Customer Resource: CRUD for personal data including file upload for KTP.

Account Resource: CRUD for bank accounts. Must be linkable to a Customer and an Agent.

Card Management: Inline relationship in Account Resource or separate CRUD to manage multiple ATM cards per account.

Help Desk: Ticketing system for Complaints where Agents can update status.

LLM Execution Instructions (Step-by-Step)
Instructions for Cursor/IDE: Please execute the following tasks based on the schema above:

Environment Setup: Create migrations for all tables using UUID.

Models: Generate Models for Customer, Account, Card, Agent, and Complaint. Define the relationships (HasMany, BelongsTo).

Security Implementation: In the Card model, implement encryption for cvv and pin_hash.

Filament Resources: - Create CustomerResource with FileUpload for KTP.

Create AccountResource with a Select for Customer and Agent.

Create CardResource or a RelationManager inside AccountResource.

Create ComplaintResource with a status badge (pending = gray, processing = blue, resolved = green).

UI/UX: Ensure all tables have a Search and Filter feature, especially for NIK, Account Number, and Agent Code.

Specific Data Sample for Testing
Customer: Wimpi Gindasari, NIK: 3213096212020011.

Bank: MANDIRI, Account: 1250016567422.

Branch: Subang, 22 Dec 2002.