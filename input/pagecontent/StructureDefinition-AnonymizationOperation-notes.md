
#### Aggregation

Given a dataset of individuals with their age and their disease.
<div>{% include aggregation.svg %}</div>
<br clear="all"/>
Their data is not anonymized.
If we know someone's age, we can know his disease.

##### k-anonymization

We made an aggregation with *k=2*.
Groups has been created with a least 2 individuals in each.

<div>{% include aggregationk2.svg %}</div>
<br clear="all"/>

Now, the problem is that if we know someone who is older than 25 years, we can say that he has disease A.

##### l-diversity

After l = 1 diversity, we have now at least one of **each** disease in **every** group.
<div>{% include aggregationl1.svg %}</div>
<br clear="all"/>

With these, we cannot be sure of the disease of an individual given his age.

But we can still statistically guess his disease.
For example, an individual older than 14 has a 2 in 3 chance of having disease B.
While with the whole data set a person has one chance in two to have disease B.

##### t-closeness

t-closeness means that each group will have the same distribution if it has the complete original data set.
In our case, each group will have the same amount of disease A as disease B.