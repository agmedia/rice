create table if not exists rice.page_category
(
    page_id  bigint unsigned not null,
    category_id bigint unsigned not null,
    constraint page_category_category_id_foreign
        foreign key (category_id) references categories (id),
    constraint page_category_page_id_foreign
        foreign key (page_id) references pages (id)
)
    engine = InnoDB
    collate = utf8mb4_unicode_ci;

create index page_category_category_id_index
    on rice.page_category (category_id);

create index page_category_page_id_index
    on rice.page_category (page_id);