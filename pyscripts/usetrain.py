# By Ethan Hughes #
# 6/25/2019 #
import pickle
import sys
import threading

vocab = {};
maxlen = 0; #set permanent value for maxlen
#working = {}; #Stores last ten things with fewest connections making them the most unique and important as they're likely not to be reached by looking at the other words and likely define the conversation the best. They will be connected to counter which determine how much longer they can stay and only once there is a free space can new knowledge come in.

#We'll take working memory together with the last set of words entered and they're connection in the rememberence stream and use them for a response.

def refwords(text):
    wordlist = [];
    while len(text) > 1:
        correct = 0;
        for length in range(1, maxlen):
            if length > len(text):
                break;
            if text[:length] in vocab and length > correct:
                correct = length;
        if correct > 0:
            wordlist.append(text[:correct]);
            #Add things to working mem with connection tolerance
            if len(vocab[text[:correct]]) < 10: #tolerance = 10
				print(text[:correct]);
            #     working[text[:correct]] = 0; #working mem life = 10
            #     if len(working) > 10:
            #         lowest = 0;
            #         lowword = '';
            #         for word in working:
            #             if working[word] < lowest:
            #                 lowest = working[word];
            #                 lowword = word;
            #         working.pop(lowword);
            text = text[correct:];
        else:
            text = text[1:];
    return wordlist;

def remember(words, activator, memlist):
    #recurs = False;
    weights = {};
    for word in words:
        for link in vocab[word]:
            if link not in words:
                if link not in weights:
                    weights[link] = vocab[word][link]/vocab[word][0];
                else:
                    weights[link] += vocab[word][link]/vocab[word][0];
    for word in weights:
        if weights[word] > activator and word not in memlist:
            memlist.append(word);
            memlist.extend(remember([word], activator+weights[word], memlist));
            #recurs = True;
    #if sum(weights.values()) > activator and recurs: #Could be switched to extending the list starting with the remembered word with the smallest combined weight
    #    memlist.extend(remember(memlist, activator+sum(weights.values()), memlist));
    #    memlist = list(dict.fromkeys(memlist)); #Not sure if this is necessary now.
    memlist = list(dict.fromkeys(memlist));
    return memlist;

def synced(line):
	pickle_in = open('vocab.pickle','rb');
	vocab = pickle.load(pickle_in);
	pickle_in.close();

	wordlist = refwords(line.lower());
	#for word in list(working):
	#	working[word] -= 1;
	#	if working[word] <= -10:
	#		working.pop(word);
	print(remember(wordlist, 0, []));
	#print(working.keys());

thread = threading.Thread(target = synced(sys.argv[0]));
thread.start();
thread.join();