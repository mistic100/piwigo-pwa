import {
    AddCommentParams,
    AddCommentResult,
    ApiConfig,
    ApiResult,
    Category,
    GetCategoriesParams,
    GetCategoryImagesParams,
    GetImageInfoParams,
    GetTagImagesParams,
    GetTagsParams,
    Image,
    ImageInfo,
    ImageWithCategories,
    ImageWithTags,
    Infos,
    LoginParams,
    Page,
    Paging,
    PwaConfig,
    RateImageParams,
    RateImageResult,
    SearchImagesParams,
    SessionStatus,
    Tag,
    Version
} from './model';

export class PiwigoApi {

    constructor(private config: ApiConfig) {

    }

    getVersion(): Promise<Version> {
        return this.call<Version>({
            method: 'GET',
            methodName: 'pwg.getVersion',
        });
    }

    getConfig(): Promise<PwaConfig> {
        return this.call<PwaConfig>({
            method: 'GET',
            methodName: 'pwa.getConfig',
        });
    }

    async getInfos(): Promise<Infos> {
        const result = await this.call<{ infos: { name: keyof Infos, value: string | number }[] }>({
            method: 'GET',
            methodName: 'pwg.getInfos',
        });

        return result.infos.reduce((result, info) => {
            // cast counters
            result[info.name] = info.name.startsWith('nb_') ? parseInt(info.value as any, 10) : info.value;
            return result;
        }, {} as Infos);
    }

    getSessionStatus(): Promise<SessionStatus> {
        return this.call<SessionStatus>({
            method: 'GET',
            methodName: 'pwg.session.getStatus',
        });
    }

    login(params: LoginParams): Promise<true> {
        return this.call<true>({
            method: 'POST',
            methodName: 'pwg.session.login',
            params,
        });
    }

    logout(): Promise<true> {
        return this.call<true>({
            method: 'GET',
            methodName: 'pwg.session.logout',
        });
    }

    getCategories(params?: GetCategoriesParams): Promise<Category[]> {
        return this.call<Category[]>({
            method: 'GET',
            methodName: 'pwg.categories.getList',
            params,
        });
    }

    async getCategoryImages(params: GetCategoryImagesParams): Promise<Page<ImageWithCategories>> {
        const result = await this.call<{ images: ImageWithCategories[], paging: Paging }>({
            method: 'GET',
            methodName: 'pwg.categories.getImages',
            params,
        });

        result.images.forEach(patchKnownFields);

        return {
            paging: result.paging,
            elements: result.images,
        };
    }

    async getImageInfo(params: GetImageInfoParams): Promise<ImageInfo> {
        const result = await this.call<ImageInfo>({
            method: 'GET',
            methodName: 'pwg.images.getInfo',
            params,
        });

        patchKnownFields(result);

        return result;
    }

    async searchImages(params: SearchImagesParams): Promise<Page<Image>> {
        const result = await this.call<{ images: Image[], paging: Paging }>({
            method: 'GET',
            methodName: 'pwg.images.search',
            params,
        });

        result.images.forEach(patchKnownFields);

        return {
            paging: result.paging,
            elements: result.images,
        };
    }

    async commentImage(params: AddCommentParams): Promise<AddCommentResult> {
        const result = await this.call<{ comment: AddCommentResult }>({
            method: 'POST',
            methodName: 'pwg.images.addComment',
            params,
        });

        return result.comment;
    }

    rateImage(params: RateImageParams): Promise<RateImageResult> {
        return this.call<RateImageResult>({
            method: 'POST',
            methodName: 'pwg.images.rate',
            params,
        });
    }

    getTags(params?: GetTagsParams): Promise<Tag[]> {
        return this.call<Tag[]>({
            method: 'GET',
            methodName: 'pwg.tags.getList',
            params,
        });
    }

    async getTagImages(params: GetTagImagesParams): Promise<Page<ImageWithTags>> {
        const result = await this.call<{ images: ImageWithTags[], paging: Paging }>({
            method: 'GET',
            methodName: 'pwg.tags.getImages',
            params,
        });

        result.images.forEach(patchKnownFields);

        return {
            paging: result.paging,
            elements: result.images,
        };
    }

    async call<TResult>({
        methodName,
        method,
        params,
    }: {
        method: 'GET' | 'POST';
        methodName: string;
        params?: Record<string, any>;
    }): Promise<TResult> {
        const searchParams = new URLSearchParams({ format: 'json' });
        let body: any;

        if (method === 'GET') {
            searchParams.append('method', methodName);

            Object.entries(params ?? {}).forEach(([key, value]) => {
                if (Array.isArray(value)) {
                    value.forEach((val) => {
                        searchParams.append(key + '[]', val);
                    });
                } else if (value !== null && value !== undefined) {
                    searchParams.append(key, value);
                }
            });
        }
        else if (method === 'POST') {
            body = {
                method: methodName,
                ...params,
            };
        }

        const url = `${this.config.host}/ws.php?` + searchParams;

        const response = await fetch(url, {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: body ? JSON.stringify(body) : undefined,
        });

        let result = await response.json() as ApiResult<TResult>;

        if (result.stat === 'ok') {
            return result.result as TResult;
        } else {
            return Promise.reject(result)
        }
    }

}

/**
 * Typecast some know fields with invalid type
 */
function patchKnownFields(image: Image) {
    ['is_gvideo', 'is_sphere'].forEach(boolKey => {
        if (boolKey in image) {
            image[boolKey] = image[boolKey] === true || image[boolKey] === '1';
        }
    });
}
