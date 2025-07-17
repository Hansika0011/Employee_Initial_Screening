import sys
import openai

client = openai.OpenAI(api_key="use_own_api_key")

def generate_questions(stack):
    prompt = f"Generate 3 technical interview questions for a candidate skilled in: {stack}"

    response = client.chat.completions.create(
        model="gpt-3.5-turbo",
        messages=[{"role": "user", "content": prompt}],
        max_tokens=300
    )

    return response.choices[0].message.content.strip()

if __name__ == "__main__":
    stack = sys.argv[1]
    print(generate_questions(stack))
