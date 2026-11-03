function csrfToken(): string {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]*)/);

    return match ? decodeURIComponent(match[1]) : '';
}

/** Calls one of the demo endpoints with the CSRF header Laravel expects. */
export function demoRequest(path: string, method: 'GET' | 'POST' = 'POST'): Promise<Response> {
    return fetch(path, {
        method,
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-XSRF-TOKEN': csrfToken(),
        },
    });
}
