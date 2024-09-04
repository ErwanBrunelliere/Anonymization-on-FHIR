validate:
	java -jar publisher.jar -ig ig.ini

clean:
	rm -rf ./output
	rm -rf ./template
	rm -rf ./temp
	rm -rf ./txCache