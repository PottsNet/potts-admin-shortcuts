# Potts Administration Shortcuts

A configurable administrator-only My Page block for webtrees 2.2.x.

## Features

- Lists enabled modules that provide a webtrees settings page
- Lets each administrator choose their most-used module settings
- Includes User administration as an optional shortcut
- Stores choices in the individual My Page block configuration
- Hides the block content from non-administrators
- Works with any webtrees theme

## Requirements

- webtrees 2.2.x
- PHP version supported by your webtrees installation
- Administrator access to enable modules and add My Page blocks

## Installation

1. Extract the release ZIP.
2. Upload the `potts_admin_shortcuts` folder to `modules_v4/`.
3. Open **Control panel > Modules > All modules** and enable **Administration shortcuts**.
4. Open **My page**, choose **Change the blocks on this page** and add **Administration shortcuts**.
5. Use the block preferences button to select the links to display.

## Notes

- The block is intended for administrator My Page use only.
- The shortcut list is built from enabled modules that expose a webtrees settings page.
- Each block instance stores its own selected shortcut list.

## Support

Please report bugs or feature requests through GitHub Issues:

https://github.com/PottsNet/potts-admin-shortcuts/issues

## Licence

Potts Administration Shortcuts is free software licensed under the GNU General Public License, version 3 or later.
