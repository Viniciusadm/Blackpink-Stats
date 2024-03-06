create table videos
(
    id           int auto_increment,
    ´key´        varchar(255) not null,
    title        varchar(255) not null,
    published_at date         not null,
    constraint videos_pk
        primary key (id)
);

create table videos_views
(
    id         int auto_increment,
    video_id   int  not null,
    views      int  not null,
    created_at date not null,
    constraint video_views_pk
        primary key (id),
    constraint video_views_videos_id_fk
        foreign key (video_id) references videos (id)
);

