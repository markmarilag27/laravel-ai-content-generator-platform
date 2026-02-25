#!/bin/bash
set -e

# 1. Configure the application user
echo "  ↳ Creating app user: ${DB_USERNAME}"
psql -v ON_ERROR_STOP=1 -U "$POSTGRES_USER" <<-EOSQL
    CREATE USER "$DB_USERNAME" WITH PASSWORD '$DB_PASSWORD';
    ALTER USER "$DB_USERNAME" NOSUPERUSER CREATEDB;
EOSQL

# 2. Provision databases
if [ -n "$POSTGRES_MULTIPLE_DATABASES" ]; then
    IFS=',' read -ra DBNAMES <<< "$POSTGRES_MULTIPLE_DATABASES"
    for db in "${DBNAMES[@]}"; do
        db=$(echo "$db" | xargs)
        if [ -n "$db" ]; then
            echo "    -> Setting up database: $db"
            psql -v ON_ERROR_STOP=0 -U "$POSTGRES_USER" -c "CREATE DATABASE $db;" || true

            psql -v ON_ERROR_STOP=1 -U "$POSTGRES_USER" -c "ALTER DATABASE $db OWNER TO \"$DB_USERNAME\";"

            echo "    -> Ownership of $db transferred to $DB_USERNAME"
        fi
    done
fi
