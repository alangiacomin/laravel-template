import globals from "globals";
import js from "@eslint/js";
import reactHooks from "eslint-plugin-react-hooks";
import reactRefresh from 'eslint-plugin-react-refresh';
import tseslint from 'typescript-eslint';


export default tseslint.config(
  // File ignorati
  {
    ignores: ['dist', 'resources/js/api'],
  },

  // Config base JS + TS
  {
    files: ['**/*.{ts,tsx}'],
    extends: [
      js.configs.recommended,
      ...tseslint.configs.recommended,
    ],
    languageOptions: {
      ecmaVersion: 2020,
      sourceType: 'module',
      globals: {
        ...globals.browser,
      },
    },
    plugins: {
      'react-hooks': reactHooks,
      'react-refresh': reactRefresh,
    },
    rules: {
      ...reactHooks.configs.recommended.rules,

      'react/prop-types': 'off',

      'react-refresh/only-export-components': [
        'warn',
        {allowConstantExport: true},
      ],

      'react-hooks/rules-of-hooks': 'error',
      'react-hooks/exhaustive-deps': 'warn',
    },
  },

  // OVERRIDE per pagine Inertia
  {
    files: ['resources/js/Pages/**/*.tsx'],
    rules: {
      '@typescript-eslint/no-unused-vars': 'off',
      'import/no-unused-modules': 'off',
      'react-refresh/only-export-components': 'off',
    }
  },
);
