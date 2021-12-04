# THIS CODE IS MY OWN WORK.
# IT WAS WRITTEN WITHOUT CONSULTING CODE WRITTEN BY OTHER STUDENTS.
# Juhao "Jerry" Zhang
# jzha652

## READ ME!!!
# This file only serves as a helper tool. This tool does not export all files, nor do they do it completely.
# i.e., Exports where a new id-key is used across two relations, e.g., Assists(), do not have a function for export.
# I don't know what i'm saying anymore whatever it's ok just dont use this file, stick with the already exported csv's

import random
import string

# Defining constants
RANDOM_STATE = 377
CHAR_POOL = string.ascii_letters + string.digits
USER_COLUMNS = [(0, 4),     # Base user
                (8, 12),    # Instructor
                (12, 12),   # TA 1
                (16, 20),   # TA 2
                (20, 24),   # TA 3
                (24, 28),   # TA 4
                (28, 32)]   # TA 5
CLASS_COLUMNS = [(4, 9)]    # 1 column to the right for instructor_id
ASSIGNMENT_COLUMNS = [[(32, 36), (4, 8)],
                      [(37, 41), (4, 8)],
                      [(42, 46), (4, 8)],
                      [(47, 51), (4, 8)],
                      [(52, 56), (4, 8)],
                      [(57, 61), (4, 8)],
                      [(62, 66), (4, 8)],
                      [(67, 71), (4, 8)],
                      [(72, 76), (4, 8)],
                      [(77, 81), (4, 8)]]
QAPOST_COLUMNS = [[(4, 7), (8, 9), (0, 4)]]
THREAD_COLUMNS = [[(11, 14), (4, 7), (8, 9)],
                  [(16, 19), (4, 7), (8, 9)],
                  [(21, 24), (4, 7), (8, 9)],
                  [(26, 29), (4, 7), (8, 9)],
                  [(31, 34), (4, 7), (8, 9)]]
TAG_QAPOST_COMP = (1, 4)
TAG_QA_COMP = (4, 7)
TAKES_CLASS_COMP = (1, 5)
TAKES_CANVAS_COMP = (2, 6)


random.seed(RANDOM_STATE)


def gen_id(id_length):
    return ''.join(random.choice(CHAR_POOL) for i in range(id_length))


def just_to_export_completes():
    with open('canvas.csv') as f:
        contents_1 = list(map(lambda a: a.strip().split(','), f))
    contents_1 = contents_1[1:]
    with open('assignment.csv') as f:
        contents_2 = list(map(lambda a: a.strip().split(','), f))
    contents_2 = contents_2[1:]

    output = []
    for i in range(len(contents_1)):
        for j in range(len(contents_2)):
            user_id = contents_1[i][0]
            for k in [(32, 36), (37, 41), (42, 46), (47, 51), (52, 56), (57, 61), (62, 66), (67, 71), (72, 76), (77, 81)]:
                if contents_1[i][k[0]:k[1]] == contents_2[j][1:5]:
                    output.append((user_id, contents_2[j][0], contents_1[i][k[1]]))

    output = list(map(lambda a: ','.join(a) + '\n', [s for s in sorted(list(set(output))) if any(s)]))
    output[-1] = output[-1].rstrip()

    print(output)
    with open('completes.csv', 'w') as out:
        out.writelines(output)


def map_files_relationship(in_file_1, in_file_2, out_file, f1_sim_columns, f2_sim_columns, f1_keep_c, f2_keep_c):
    with open(in_file_1) as f:
        contents_1 = list(map(lambda a: a.strip().split(','), f))
    contents_1 = contents_1[1:]
    with open(in_file_2) as f:
        contents_2 = list(map(lambda a: a.strip().split(','), f))
    contents_2 = contents_2[1:]

    output = []
    for i in range(len(contents_1)):
        for j in range(len(contents_2)):
            if contents_1[i][f1_sim_columns[0]:f1_sim_columns[1]] == contents_2[j][f2_sim_columns[0]:f2_sim_columns[1]]:
                output.append(tuple(contents_1[i][f1_keep_c[0]:f1_keep_c[1]] + contents_2[j][f2_keep_c[0]:f2_keep_c[1]]))

    print(output)

    output = list(map(lambda a: ','.join(a) + '\n', [s for s in sorted(list(set(output))) if any(s)]))
    output[-1] = output[-1].rstrip()

    print(output)
    with open(out_file, 'w') as out:
        out.writelines(output)


def extract(in_file, out_file, header, columns, simple=True, generate_id=False, id_length=10):
    with open(in_file) as f:
        contents = list(map(lambda a: a.strip().split(','), f))
    contents = contents[1:]

    output = []

    if simple:
        for row in contents:
            for c in columns:
                output.append(tuple(row[c[0]:c[1]]))
    else:
        for row in contents:
            for c in columns:
                r_tuple = tuple()
                for sc in c:
                    r_tuple += tuple(row[sc[0]:sc[1]])
                output.append(r_tuple)

    # poggers Python
    # Eliminates duplicate and empty rows, then formats row into csv (\n-terminated) and converts rows to list
    output = list(map(lambda a: ','.join(a) + '\n', [s for s in sorted(list(set(output))) if any(s)]))

    # If we have to generate ID, do so without creating duplicates
    ids = set()
    if generate_id:
        while len(ids) < len(output):
            identifier = gen_id(id_length)
            if identifier not in ids:
                ids.add(identifier)
        ids = list(ids)
        output = sorted(["{},{}".format(ids[i], output[i]) for i in range(len(ids))])

    output[-1] = output[-1].rstrip()

    with open(out_file, 'w') as out:
        out.write(header)
        out.writelines(output)


def extract_user():
    extract('canvas.csv', 'user.csv', 'id,net_id,fname,lname\n', USER_COLUMNS)


def extract_class():
    extract('canvas.csv', 'class.csv', 'id,course_no,course_name,semester,year,lecturer_id\n', CLASS_COLUMNS,
            generate_id=True)


def extract_assignment():
    extract('canvas.csv', 'assignment.csv', 'id,name,due_date,description,points,class_id\n', ASSIGNMENT_COLUMNS,
            simple=False, generate_id=True)


def extract_qapost():
    extract('qa.csv', 'qapost.csv', 'id,title,post_date,text,poster_id,class_id\n', QAPOST_COLUMNS, simple=False,
            generate_id=True)


def extract_thread():
    extract('qa.csv', 'thread.csv', 'id,post_date,text,poster_id,parent_id\n', THREAD_COLUMNS, simple=False,
            generate_id=True)


def extract_tag():
    map_files_relationship('qapost.csv', 'qa.csv', 'tags.csv', TAG_QAPOST_COMP, TAG_QA_COMP, (0, 1), (7, 8))


def extract_takes():
    map_files_relationship('class.csv', 'canvas.csv', 'takes.csv', TAKES_CLASS_COMP, TAKES_CANVAS_COMP, (0, 1), (0, 2))


functions = {'1': extract_user,
             '2': extract_class,
             '3': extract_assignment,
             '4': extract_qapost,
             '5': extract_thread,
             '6': extract_tag,
             '7': extract_takes}


def main():
    while 1:
        print("Welcome to the interactive CSV extractor tool!\n"
              "Please select an option from below.\n"
              "1 - Extract a relation\n"
              "2 - View relations\n"
              "3 - Exit")
        option = input('> ')
        if option == '1':
            while 1:
                print("Please enter the file to extract from below.\n"
                      "1 - user.csv\n"
                      "2 - class.csv\n"
                      "3 - assignment.csv\n"
                      "4 - qapost.csv\n"
                      "5 - thread.csv\n"
                      "6 - tags.csv\n"
                      "7 - takes.csv")
                extract_option = input('> ')
                if extract_option in ['1', '2', '3', '4', '5', '6', '7']:
                    a = input("Are you sure? (y) > ")
                    if a != 'y':
                        print("Looks like you weren't sure. Exiting")
                        exit(0)
                    functions[extract_option]()
                    print("Extracted. Byebye!")
                    exit(0)
                elif extract_option == '0':
                    break
                else:
                    print("Please enter option number 0 through 9.")
        elif option == '2':
            print("Keys are delimited by brackets.\n"
                  "The relational model looks like the following:")
        elif option == '3':
            print("Byebye!")
            exit(0)
        else:
            print("Please enter option number 1, 2, or 3.")


if __name__ == '__main__':
    main()
