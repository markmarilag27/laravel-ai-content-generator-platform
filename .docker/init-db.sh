#!/bin/bash
set -e

echo "Starting custom database initialization..."

# 1. Create the application user and set permissions (ignores error if user exists)
psql -v ON_ERROR_STOP=0 -U "$POSTGRES_USER" -c "CREATE USER \"$DB_USERNAME\" WITH PASSWORD '$DB_PASSWORD';" || true
psql -v ON_ERROR_STOP=0 -U "$POSTGRES_USER" -c "ALTER USER \"$DB_USERNAME\" NOSUPERUSER;" || true

# 2. Check if the multiple databases variable exists
if [ -n "$POSTGRES_MULTIPLE_DATABASES" ]; then
    echo "Multiple databases requested: $POSTGRES_MULTIPLE_DATABASES"

    # Split the comma-separated string into an array and loop through it
    IFS=',' read -ra DBOBJ <<< "$POSTGRES_MULTIPLE_DATABASES"
    for db in "${DBOBJ[@]}"; do
        echo "Creating database: $db..."

        # Create the database and grant privileges (ignores errors if it already exists)
        psql -v ON_ERROR_STOP=0 -U "$POSTGRES_USER" -c "CREATE DATABASE $db;" || true
        psql -v ON_ERROR_STOP=0 -U "$POSTGRES_USER" -c "GRANT ALL PRIVILEGES ON DATABASE $db TO \"$DB_USERNAME\";" || true
    done

    echo "Databases created successfully!"
else
    echo "No extra databases defined in POSTGRES_MULTIPLE_DATABASES."
fi
