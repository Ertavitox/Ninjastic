import { useAuthStore } from '@/stores/auth.store';


interface RequestOptions {
    method: string;
    headers: Record<string, string>;
    body?: string;
}

interface ResponseData {
    message?: string;
}

export const fetchWrapper = {
    get: request('GET'),
    post: request('POST'),
    put: request('PUT'),
    delete: request('DELETE')
};

function request(method: string) {
    return (url: string, body?: Record<string, any>) => {
        const requestOptions: RequestOptions = {
            method,
            headers: authHeader(url)
        };
        if (body) {
            requestOptions.headers['Content-Type'] = 'application/json';
            requestOptions.body = JSON.stringify(body);
        }
        return fetch(url, requestOptions).then(handleResponse);
    };
}


function authHeader(url: string): Record<string, string> {

    const { user } = useAuthStore();
    const isLoggedIn = !!user?.token;
    const isApiUrl = url.startsWith(import.meta.env.VITE_API_URL);
    if (isLoggedIn && isApiUrl) {
        return { Authorization: `Bearer ${user.token}` };
    } else {
        return {};
    }
}

function handleResponse(response: Response): Promise<ResponseData> {
    return response.text().then(text => {
        let data: ResponseData | null = null;

        if (text) {
            data = JSON.parse(text) as ResponseData;
        }

        if (!response.ok) {
            const { user, logout } = useAuthStore();
            if ([401, 403].includes(response.status) && user) {
                logout();
            }
            const error = (data && data.message) || response.statusText;
            return Promise.reject(error);
        }

        return data as ResponseData; 
    });
}
