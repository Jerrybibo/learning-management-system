# Create canvas_db database
create database if not exists canvas_db;
use canvas_db;

# Create user table
create table user (
    id     CHAR(10)    not null,
    net_id VARCHAR(64) not null,
    fname  TINYTEXT    not null,
    lname  TINYTEXT    not null,
    constraint user_pk
        primary key (id)
);

create unique index user_net_id_uindex
    on user (net_id);

# Create class table
create table class (
    id          CHAR(10)   not null,
    course_no   CHAR(8)    not null,
    course_name TINYTEXT   not null,
    semester    VARCHAR(6) not null,
    year        INT        not null,
    lecturer_id CHAR(10)   not null,
    constraint class_pk
        primary key (id)
);

# Create assignment table
create table assignment (
    id          CHAR(10)    not null,
    name        VARCHAR(64) not null,
    due_date    TIMESTAMP   not null,
    description TEXT        not null,
    points      INT         not null,
    class_id    CHAR(10)    not null,
    constraint assignment_pk
        primary key (id)
);

# Create qapost table
create table qapost (
    id          CHAR(10)    not null,
    title       TINYTEXT    not null,
    post_date   TIMESTAMP   not null,
    text        TEXT        not null,
    poster_id   CHAR(10)    not null,
    class_id    CHAR(10)    not null,
    constraint qapost_pk
        primary key (id),
    constraint qapost_user_fk
        foreign key (poster_id) references user (id)
        on delete cascade on update cascade,
    constraint qapost_class_fk
        foreign key (class_id) references class (id)
        on delete cascade on update cascade
);

# Create thread table
create table thread (
    id          CHAR(10)    not null,
    post_date   TIMESTAMP   not null,
    text        TEXT        not null,
    poster_id   CHAR(10)    not null,
    parent_id   CHAR(10)    not null,
    constraint thread_pk
        primary key (id),
    constraint thread_user_fk
        foreign key (poster_id) references user (id)
        on delete cascade on update cascade,
    constraint thread_qapost_fk
        foreign key (parent_id) references qapost (id)
        on delete cascade on update cascade
);

# Create tags table
create table tags (
    post_id     CHAR(10)    not null,
    tag         VARCHAR(32) not null,
    constraint tags_pk
        primary key (post_id, tag),
    constraint tags_qapost_fk
        foreign key (post_id) references qapost (id)
        on delete cascade on update cascade
);

# Create takes table
create table takes (
    user_id         CHAR(10)    not null,
    class_id        CHAR(10)    not null,
    letter_grade    VARCHAR(3),
    constraint takes_pk
        primary key (user_id, class_id),
    constraint takes_user_fk
        foreign key (user_id) references user (id)
        on delete cascade on update cascade,
    constraint takes_class_fk
        foreign key (class_id) references class (id)
        on delete cascade on update cascade
);

# Create completes table
create table completes (
    user_id         CHAR(10)    not null,
    assignment_id   CHAR(10)    not null,
    grade           VARCHAR(4),
    constraint completes_pk
        primary key (user_id, assignment_id),
    constraint completes_user_fk
        foreign key (user_id) references user (id)
        on delete cascade on update cascade,
    constraint completes_assignment_fk
        foreign key (assignment_id) references assignment (id)
        on delete cascade on update cascade
);

# Create assists table
create table assists (
    user_id     CHAR(10)    not null,
    class_id    CHAR(10)    not null,
    constraint assists_pk
        primary key (user_id, class_id),
    constraint assists_user_fk
        foreign key (user_id) references user (id),
    constraint assists_class_fk
        foreign key (class_id) references class (id)
);
