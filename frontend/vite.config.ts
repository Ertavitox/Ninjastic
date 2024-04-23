import { fileURLToPath, URL } from 'node:url';
import { defineConfig, loadEnv, type ConfigEnv } from 'vite'
import vue from '@vitejs/plugin-vue';
import vueJsx from '@vitejs/plugin-vue-jsx';
import mkcert from 'vite-plugin-mkcert'

// https://vitejs.dev/config/
export default ({ mode }: ConfigEnv): any =>
  defineConfig({
    base: loadEnv(mode, process.cwd(), '')['BASE'] ?? '/',
    plugins: [vue(), vueJsx(), mkcert()],
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./src', import.meta.url))
      },
      extensions: [
        '.js',
        '.json',
        '.jsx',
        '.mjs',
        '.ts',
        '.tsx',
        '.vue',
    ],
    }
  });


  


