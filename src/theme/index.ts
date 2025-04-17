// src/theme/index.ts
import type { ThemeConfig } from 'antd';

const theme: ThemeConfig = {
  token: {
    colorPrimary: '#0ea5e9', // Your brand blue
    borderRadius: 6,
    colorBgContainer: '#ffffff',
  },
  components: {
    Button: {
      colorPrimary: '#0ea5e9',
      algorithm: true,
    },
    DatePicker: {
      colorPrimary: '#0ea5e9',
    },
  }
};

export default theme;