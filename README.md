# WHMCS Decrypt Custom Fields in Tickets

A hack to show the decrypted version of password custom fields in tickets to the client.

This hook loops through each custom field and only decrypts fields that are of the password type.

To exclude certains fields from the decryption simply add the custom field name to the $excludeFields variable:

```
$excludeFields = []; // e.g. 'cPanel Password'
```

## How to install

1. Copy the ```includes``` folder to your root WHMCS directory.

2. Open the viewticket.tpl file in your templates directory and add the following to the end:

```
{$customfieldjavascript}
```

## Have a feature request?

Any ideas for it please let me know! I'm happy to implement anything that may benefit the module further. Email all feature requests to lee@leemahoney.dev

## Contributions

Feel free to fork the repo, make changes, then create a pull request!