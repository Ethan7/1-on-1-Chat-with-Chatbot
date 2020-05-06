pickle_in = open('vocab.pickle','rb');
vocab = pickle.load(pickle_in);
pickle_in.close();

maxlen = 0;
for word in vocab:
	if len(word) > maxlen:
		maxlen = len(word);

$vocab = {[]};
dictionary = open('dictionary.csv', 'r');
reader = csv.reader(dictionary);
read = list(reader);
for row in read:
    word = row[0].lower();
    words = row[2].replace(',', '').replace(';','').replace('.', '').replace('!', '').replace('?', '').replace('(','').replace(')','').replace('-','').replace('\"', '').replace('  ',' ').lower();
    wordlist = list(dict.fromkeys(words.split(' ')));
    activation = [10]*(len(wordlist));
    newword = dict(zip(wordlist, activation));
    #print(row[0]);
    if word not in vocab:
        vocab[word] = newword;
        vocab[word][0] = 1;
        #addword(word, trie);
        if len(word) > maxlen:
            maxlen = len(word);
    else:
        vocab[word][0] += 1;
        for linkword in wordlist:
            if linkword not in vocab[word]:
                vocab[word][linkword] = 1;
            else:
                vocab[word][linkword] += 1;
dictionary.close();