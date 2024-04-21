import { useAuthStore } from '@/stores/auth.store'

interface RequestOptions {
  method: string
  headers: Record<string, string>
  body?: string
}

interface ResponseData {
  token: string
}

export const fetchWrapper = {
  get: request('GET'),
  post: request('POST'),
  put: request('PUT'),
  delete: request('DELETE')
}

function request(method: string) {
  return (url: string, body?: Record<string, any>) => {
    const auth = useAuthStore()
    const requestOptions: RequestOptions = {
      method,
      headers: authHeader(url, auth)
    }
    if (body) {
      requestOptions.headers['Content-Type'] = 'application/json'
      requestOptions.body = JSON.stringify(body)
    }
    return fetch(url, requestOptions).then(handleResponse)
  }
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
  return response.text().then((text) => {
    const data = text && JSON.parse(text)

    if (response.status !== 200) {
      const { userData, logout } = useAuthStore()
      // Ha a visszakapott kódunk 401 vagy 403 akkor jelentkeztessük ki a "felhasználót".
      if ([401, 403].includes(response.status) && userData) {
        logout()
      }
      const error = (data && data.message) || response.statusText
      return Promise.reject(error)
    }

    return data
  })
}
