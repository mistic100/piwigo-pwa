export type ApiResult<TResult> = {
    stat: 'ok' | 'fail';
    result?: TResult;
    err?: number;
    message?: string;
};

export type Pageable = {
    per_page?: number;
    page?: number;
    order?: string;
};

export type Paging = {
    page: number;
    per_page: number;
    count: number;
    total_count: number;
};

export type Page<TResult> = {
    paging: Paging;
    elements: TResult[];
}

export type Version = string;

export type ImageSize = 'square' | 'thumb' | '2small' | 'xsmall' | 'small' | 'medium' | 'large' | 'xlarge' | 'xxlarge' | '3xlarge' | '4xlarge';

// added via pwa plugin
export type PwaConfig = {
    gallery_locked: boolean;
    gallery_title: string;
    page_banner: string;
    week_starts_on: string;
    image_order: string;
    category_url_style: 'id' | 'id-name';
    picture_url_style: 'id' | 'id-file' | 'file';
    tag_url_style: 'id' | 'id-tag' | 'tag';

    activate_comments: boolean;
    comments_forall: boolean;
    comments_order: string;
    comments_author_mandatory: boolean;
    comments_email_mandatory: boolean;
    comments_enable_website: boolean;
    comments_validation: boolean;
    user_can_delete_comment: boolean;
    user_can_edit_comment: boolean;

    rate: boolean;
    rate_anonymous: boolean;
    rate_items: number[];

    nb_categories_page: number;
    nb_comment_page: number;

    derivatives: Partial<Record<ImageSize, DerivativeConfig>>;
    picture_informations: {
        author: boolean;
        created_on: boolean;
        dimensions: boolean;
        categories: boolean;
        visits: boolean;
        posted_on: boolean;
        file: boolean;
        filesize: boolean;
        tags: boolean;
        rating_score: boolean;
    };
};

export type DerivativeConfig = {
    sizing: {
        ideal_size: [number, number];
        max_crop: number;
        min_size: [number, number];
    };
};

export type Infos = {
    version: Version;
    nb_elements: number;
    nb_categories: number;
    nb_virtual: number;
    nb_physical: number;
    nb_image_category: number;
    nb_tags: number;
    nb_image_tag: number;
    nb_users: number;
    nb_groups: number;
    nb_comments: number;
    nb_unvalidated_comments: number;
    first_date: string; // yyyy-mm-dd HH:MM:SS
    cache_size: number;
};

export type SessionStatus = {
    username: 'guest' | string;
    status: 'webmaster' | 'admin' | 'normal' | 'generic' | 'guest';
    theme: string;
    language: string;
    pwg_token: string;
    charset: string;
    current_datetime: string; // yyyy-mm-dd HH:MM:SS
    version: Version;
    save_visits: boolean;
    connected_with: string;
    available_sizes: ImageSize[];
};

export type LoginParams = {
    username: string;
    password?: string;
};

export type GetCategoriesParams = {
    cat_id?: number;
    recursive?: boolean;
    public?: boolean;
    tree_output?: boolean;
    fullname?: boolean;
    search?: string;
    thumbnail_size?: ImageSize;
};

// added via pwa plugin
export type GetCategoryInfoParams = {
    cat_id: number;
    public?: boolean;
};

export type Category = {
    id: number;
    name: string;
    comment?: string;
    permalink?: string;
    status: 'public' | unknown,
    uppercats: string;
    global_rank: string;
    id_uppercat?: number;
    nb_images: number;
    total_nb_images: number;
    representative_picture_id: number;
    date_last?: string; // yyyy-mm-dd HH:MM:SS
    max_date_last: string; // yyyy-mm-dd HH:MM:SS
    nb_categories: number;
    url: string;
    name_raw: string;
    comment_raw?: string;
    tn_url: string;
};

export type CategoryWithSubCategories = Category & {
    sub_categories?: CategoryWithSubCategories[];
};

// added via pwa plugin
export type CategoryInfo = Category & {
    tn_url: never;
    parents: Array<{
        id: number;
        name: string;
    }>;
};

export type GetCategoryImagesParams = Pageable & {
    cat_id?: number | number[];
    recursive?: boolean;
};

export type Derivative = {
    url: string;
    width: number;
    height: number;
};

export type Image = {
    is_favorite: boolean;
    id: number;
    width: number;
    height: number;
    hit: number;
    file: string;
    name: string;
    comment?: string;
    date_creation?: string; // yyyy-mm-dd HH:MM:SS
    date_available?: string; // yyyy-mm-dd HH:MM:SS
    page_url: string;
    element_url: string;
    download_url: string;
    derivatives: Record<ImageSize, Derivative>;
    // core but added via pwa plugin
    latitude?: number;
    longitude?: number;
    // plugins, added via pwa plugin
    is_gvideo?: boolean;
    is_sphere?: boolean;
};

export type ImageWithCategories = Image & {
    categories: {
        id: number;
        url: string;
        page_url: string;
    }[];
};

export type ImageWithTags = Image & {
    rank: number;
    tags: {
        id: number;
        url: string;
        page_url: string;
    }[];
};

export type GetImageInfoParams = {
    image_id: number;
    comments_per_page?: number;
    comments_page?: number;
    // added via pwa plugin
    content_cat_id?: number;
    content_tag_id?: number;
    content_order?: string;
};

export type ImageInfo = Image & {
    author?: string;
    filesize: number;
    coi?: string;
    representative_ext?: string;
    date_metadata_update?: string; // yyyy-mm-dd
    rating_score?: number;
    level?: number;
    md5sum: string;
    added_by: string; // user id
    rotation?: number;
    lastmodified: string; // yyyy-mm-dd MM-DD-SS
    name_raw: string;
    comment_raw?: string;
    rates: {
        average?: number;
        count: number;
        score?: number;
    };
    categories: {
        id: number;
        name: string;
        permalink?: string;
        uppercats: string;
        global_rank: string;
        url: string;
        page_url: string;
    }[];
    tags: {
        id: number;
        name: string;
        url_name: string;
        lastmodified: string; // yyyy-mm-dd MM-DD-SS
        url: string;
        page_url: string;
    }[];
    comment_post?: {
        author: string;
        key: string;
    };
    comments_paging: Paging;
    comments: {
        id: number;
        date: string; // yyyy-mm-dd MM-DD-SS
        author: string;
        content: string;
    }[];
    // added via pwa plugin
    navigation?: {
        nb_images: number;
        current_rank: number;
        previous_item?: {
            id: number;
            name: string;
        };
        next_item?: {
            id: number;
            name: string;
        };
    };
};

export type SearchImagesParams = Pageable & {
    query: string;
};

export type AddCommentParams = {
    image_id: number;
    author?: string;
    content: string;
    key: string;
};

export type AddCommentResult = {
    id: number;
    validation: boolean;
};

export type RateImageParams = {
    image_id: number;
    rate: 0 | 1 | 2 | 3 | 4 | 5;
};

export type RateImageResult = {
    score: number;
    average: number;
    count: number;
};

export type GetTagsParams = {
    sort_by_counter?: boolean;
};

export type Tag = {
    id: number;
    name: string;
    url_name: string;
    lastmodified: string; // yyyy-mm-dd MM-DD-SS
    counter: number;
    url: string;
};

export type GetTagImagesParams = Pageable & {
    tag_id?: number | number[];
    tag_url_name?: string | string[];
    tag_name?: string | string[];
    tag_mode_and?: boolean;
};
