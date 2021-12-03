# THIS CODE IS MY OWN WORK.
# IT WAS WRITTEN WITHOUT CONSULTING CODE WRITTEN BY OTHER STUDENTS.
# Juhao "Jerry" Zhang
# jzha652

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


random.seed(RANDOM_STATE)


def gen_id(id_length):
    return ''.join(random.choice(CHAR_POOL) for i in range(id_length))


def simple_extract(in_file, out_file, header, columns, generate_id=False, id_length=10):
    with open(in_file) as f:
        contents = list(map(lambda a: a.strip().split(','), f))
    contents = contents[1:]

    output = []
    for row in contents:
        for c in columns:
            output.append(tuple(row[c[0]:c[1]]))

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
    simple_extract('canvas.csv', 'user.csv', 'id,net_id,fname,lname\n', USER_COLUMNS)


def extract_class():
    simple_extract('canvas.csv', 'class.csv', 'id,course_no,course_name,semester,year,lecturer_id\n', CLASS_COLUMNS,
                   generate_id=True)


def extract_assignment():
    pass


def extract_qapost():
    pass


functions = {'1': extract_user,
             '2': extract_class}


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
                      "2 - class.csv")
                extract_option = input('> ')
                if extract_option in ['1', '2']:
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
