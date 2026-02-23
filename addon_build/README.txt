Auction Addon - Installation
============================

1. ZIP file: auction_addon.zip (in this folder) is ready to install.

2. To install:
   - Log in as Admin.
   - Go to Addons (from admin sidebar).
   - Click "Upload addon" / "Install addon" and choose auction_addon.zip.
   - Submit. The addon will be installed and activated.

3. If you prefer to install without the ZIP (files are already in the project):
   - Add a row to the `addons` table:
     INSERT INTO addons (name, unique_identifier, version, activated, created_at, updated_at)
     VALUES ('Auction', 'auction', '1.0', 1, NOW(), NOW());
   - The controllers, service, and views are already in app/ and resources/views/auction/.

4. After installation, enable "Auction" in your theme's homepage sections if needed,
   and set "Seller can add auction product" in Business Settings if you want sellers to create auction products.
