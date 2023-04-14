/**
 * Returns request's response as JSON.
 * @param {Object} body - Body of the POST request
 * @param {Boolean} [json=false] - False: body is url encoded, True: body is a json object.
 * @returns {String} - JSON encoded response echoed from request
 */
async function fetchPostJSON(body, json = false) {
    const type = json
        ? "application/json"
        : 'application/x-www-form-urlencoded; charset="utf-8"';
    let res;
    res = await fetch("/", {
        method: "POST",
        headers: {
            "Content-Type": type,
            Accept: "application/json",
        },
        body: json ? JSON.stringify(body) : body,
    })
        .then((Response) => Response.json())
        .then((data) => {
            if (data["error"]) throw data;
            return data;
        })
        .catch((error) => {
            console.error(error["error"]);
            if (error["data"]) console.debug(error["data"]);
        });
    return res;
}
