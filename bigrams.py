import string
import sys

text_file = open("learn/data/book.txt", "r")
filecontents= text_file.read()
       
# count bigrams
bigrams = {}
words_punct = filecontents.split()
words = [ w.strip(string.punctuation).lower() for w in words_punct ]

# add special START, END tokens
words = ["START"] + words + ["END"]

for index, word in enumerate(words):
    if index < len(words) - 1:
        w1 = words[index]
        w2 = words[index + 1]
        bigram = (w1, w2)

        if bigram in bigrams:
            bigrams[ bigram ] = bigrams[ bigram ] + 1
        else:
            bigrams[ bigram ] = 1

# sort bigrams by their counts
sorted_bigrams = sorted(bigrams.items(), key = lambda pair:pair[1], reverse = True)

# for bigram, count in sorted_bigrams:
#     print bigram, ":", count

word = input("Enter word:")
match = "" 
for bigram in bigrams:
    if bigram[0] == word:
        match = bigram
        break;

print bigram[1]
