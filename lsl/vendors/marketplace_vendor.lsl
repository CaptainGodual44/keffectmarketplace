// Marketplace Vendor (Draft)
// Demonstrates permission-based L$ debit authorization and signed intent submission.
// NOTE: Canonical API contract requires HMAC-SHA256(payload|timestamp|nonce, shared_secret).
// If unavailable in LSL runtime, use a trusted relay to sign requests.

string API_URL = "https://example.com/api/lsl/purchase-intent";
string OBJECT_ID = "object-uuid";
string PRODUCT_SKU = "PROD-001";
integer PRICE_L$ = 150;
key currentUser;

string buildPayload()
{
    return llList2Json(JSON_OBJECT, [
        "avatar_id", (string)currentUser,
        "product_sku", PRODUCT_SKU,
        "quantity", 1,
        "currency", "L$",
        "amount", PRICE_L$
    ]);
}

default
{
    touch_start(integer total_number)
    {
        currentUser = llDetectedKey(0);
        llRequestPermissions(currentUser, PERMISSION_DEBIT);
    }

    run_time_permissions(integer perm)
    {
        if (perm & PERMISSION_DEBIT)
        {
            string payload = buildPayload();
            string timestamp = (string)llGetUnixTime();
            string nonce = (string)llFrand(99999999.0);

            // Placeholder signature - do NOT use in production.
            // Replace with relay-generated HMAC-SHA256 matching API contract.
            string signature = llSHA1String(payload + "|" + timestamp + "|" + nonce);

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
