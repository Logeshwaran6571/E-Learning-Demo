
import sys

def count_braces(filename, start, end):
    with open(filename, 'r', encoding='utf-8') as f:
        lines = f.readlines()
        script = "".join(lines[start:end])
        open_count = script.count('{')
        close_count = script.count('}')
        print(f"Open: {open_count}, Close: {close_count}")

if __name__ == "__main__":
    count_braces(r'c:\xampp\htdocs\L-elearining demo\E-Learning-Demo\app\Views\workflow.php', 1035, 1241)
