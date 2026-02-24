#!/bin/bash
set -e

psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    -- 1. Create the application user using the DB_USERNAME from your .env
    -- We use the shell variable $POSTGRES_USER to log in as the superuser first
    CREATE USER "$DB_USERNAME" WITH PASSWORD '$DB_PASSWORD';

    -- 2. Grant permissions on the database created by Docker
    GRANT ALL PRIVILEGES ON DATABASE "$POSTGRES_DB" TO "$DB_USERNAME";

    -- 3. Grant schema permissions
    GRANT ALL ON SCHEMA public TO "$DB_USERNAME";

    -- 4. Ensure it is NOT a superuser so RLS works
    ALTER USER "$DB_USERNAME" NOSUPERUSER;
EOSQL
