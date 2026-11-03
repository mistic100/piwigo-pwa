import type { AppConfig } from '#lib';
import type { PiwigoApi } from '#lib/api/index.js';
import type { Category, CategoryInfo, ImageInfo, Tag } from '#lib/api/model.js';

// See https://svelte.dev/docs/kit/types#app.d.ts
// for information about these interfaces
declare global {
	namespace App {
		// interface Error {}
		// interface Locals {}
		interface PageData {
			// always loaded
			config: AppConfig;
			client: PiwigoApi;
			rootCategories: Category[];
			allTags: Tag[];
			// depends on current page
			isRoot?: boolean;
			category?: CategoryInfo;
			picture?: ImageInfo;
			tag?: Tag;
		}
		// interface PageState {}
		// interface Platform {}
	}
}

export { };
