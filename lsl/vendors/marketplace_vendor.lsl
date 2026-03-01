// Marketplace Vendor (Draft)
// Demonstrates permission-based L$ debit authorization and signed intent submission.

string API_URL = "https://example.com/api/lsl/purchase-intent";
string OBJECT_ID = "object-uuid";
string SHARED_SECRET = "replace_with_secret";
string PRODUCT_SKU = "PROD-001";
integer PRICE_L$ = 150;
key currentUser;

string canonicalSigningString(string payload, string timestamp, string nonce)
{
    return payload + "|" + timestamp + "|" + nonce;
}

string buildSignature(string payload, string timestamp, string nonce)
{
    string signingInput = canonicalSigningString(payload, timestamp, nonce);

    // Canonical contract: lowercase hexadecimal HMAC-SHA256 over "payload|timestamp|nonce".
    return llToLower(llHMAC(SHARED_SECRET, signingInput, "sha256"));
}

default
{
    touch_start(integer total_number)
    {
        currentUser = llDetectedKey(0);
        // Request permission from the avatar to debit L$.
        llRequestPermissions(currentUser, PERMISSION_DEBIT);
    }

    run_time_permissions(integer perm)
    {
        if (perm & PERMISSION_DEBIT)
        {
            integer unixTs = llGetUnixTime();
            string timestamp = (string)unixTs;
            string nonce = (string)llFrand(99999999.0);

            // Optional direct debit step depending on in-world transaction design.
            // integer debitResult = llGiveMoney(llGetOwner(), PRICE_L$);

            string payload = llList2Json(JSON_OBJECT, [
                "avatar_id", (string)currentUser,
                "product_sku", PRODUCT_SKU,
                "quantity", 1,
                "currency", "L$",
                "amount", PRICE_L$
            ]);

            string signature = buildSignature(payload, timestamp, nonce);

            llHTTPRequest(
                API_URL,
                [
                    HTTP_METHOD, "POST",
                    HTTP_MIMETYPE, "application/json",
                    HTTP_CUSTOM_HEADER, "X-LSL-OBJECT-ID", OBJECT_ID,
                    HTTP_CUSTOM_HEADER, "X-LSL-TIMESTAMP", timestamp,
                    HTTP_CUSTOM_HEADER, "X-LSL-NONCE", nonce,
                    HTTP_CUSTOM_HEADER, "X-LSL-SIGNATURE", signature
                ],
                payload
            );
        }
        else
        {
            llOwnerSay("User denied debit permission; purchase cancelled.");
        }
    }

    http_response(key request_id, integer status, list metadata, string body)
    {
        if (status >= 200 && status < 300)
        {
            llOwnerSay("Marketplace accepted purchase intent: " + body);
        }
        else
        {
            llOwnerSay("Marketplace error: HTTP " + (string)status + " | " + body);
        }
    }
}
