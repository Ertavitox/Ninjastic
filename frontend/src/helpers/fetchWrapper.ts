import { useAuthStore } from '@/stores/auth.store'

interface RequestOptions {
  method: string
  headers: Record<string, string>
  body?: string
}

interface ResponseData {
  status: number
  user?: any 
  token?: string
  refresh_token?: string
}

export const fetchWrapper = {
  get: request('GET'),
  post: request('POST'),
  put: request('PUT'),
  delete: request('DELETE')
}

function request(method: string) {
  return async (url: string, body?: Record<string, any>) => {
    const auth = useAuthStore();
    const requestOptions: RequestOptions = {
      method,
      headers: authHeader(url, auth)
    };
    if (body) {
      requestOptions.headers['Content-Type'] = 'application/json';
      requestOptions.body = JSON.stringify(body);
    }
    const response = await fetch(url, requestOptions);
    
    return handleResponse(response);
  };
}


function authHeader(url: string, auth: any): Record<string, string> {
  const token = auth.getToken()
  const userData = auth.getUserData()
  const isLoggedIn = !!userData && !!token
  const isApiUrl = url.startsWith(import.meta.env.VITE_API_URL)
  if (isLoggedIn && isApiUrl) {
    return { Authorization: `Bearer ${token}` }
  } else {
    return {}
  }
}

function handleResponse(response: Response): Promise<ResponseData> {
  if (!response.ok) {
    const { userData, logout } = useAuthStore();
    if ([401, 403].includes(response.status) && userData) {
      logout();
    }
    return response.text().then((text) => {
      const data = text && JSON.parse(text);
      const error = (data && data.message) || response.statusText;
      return Promise.reject(error);
    });
  } else {
    return response.json().then(data => ({
      status: response.status,
      user: data.user,
      token: data.token,
      refresh_token: data.refresh_token 
    }));
  }
}

