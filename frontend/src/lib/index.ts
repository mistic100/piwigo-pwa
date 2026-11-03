// place files you want to import through the `#lib` alias in this folder.

import type { PwaConfig } from './api/model';

export type FrontendConfig = {
    backendUrl: string;
    displayBanner: boolean;
};

export type AppConfig = PwaConfig & FrontendConfig;
