-- StudyCircle DEMO DATA ONLY. Import after schema.sql and only once.
-- Demo people/groups/resources are fictional and intended for local coursework demos.
USE studycircle;

INSERT INTO users (name,email,password_hash,role,status) VALUES
('StudyCircle Student','student@studycircle.test','$2y$12$9lasVUQ3BkvMzYTN4kKD.uSKZ9xwl1NLS0gSIN.yme0MCEX/9g.FC','student','active'),
('StudyCircle Admin','admin@studycircle.test','$2y$12$Heqv10mMflC1J9Jj3ZfXkOOHgoi2rh4sIuP1KWxLs97AprTX/ZsVi','admin','active'),
('Nethmi Perera','nethmi.demo@studycircle.test','$2y$12$ffJyPNZhwhozayykLTG6wuugqOHUp2/9n8fnW8L//pcMs6b8xSOwC','student','active'),
('Kavindu Silva','kavindu.demo@studycircle.test','$2y$12$ffJyPNZhwhozayykLTG6wuugqOHUp2/9n8fnW8L//pcMs6b8xSOwC','student','active'),
('Amaya Fernando','amaya.demo@studycircle.test','$2y$12$ffJyPNZhwhozayykLTG6wuugqOHUp2/9n8fnW8L//pcMs6b8xSOwC','student','active'),
('Ravindu Jayasuriya','ravindu.demo@studycircle.test','$2y$12$ffJyPNZhwhozayykLTG6wuugqOHUp2/9n8fnW8L//pcMs6b8xSOwC','student','active')
ON DUPLICATE KEY UPDATE name=VALUES(name), password_hash=VALUES(password_hash), role=VALUES(role), status=VALUES(status);

INSERT INTO subjects(name,description) VALUES
('Algorithms & Data Structures','Core algorithms, data structures, complexity, and problem solving.'),
('Artificial Intelligence','Search, reasoning, machine learning, and AI foundations.'),
('Object Oriented Programming','Classes, objects, inheritance, and software design.'),
('Database Management Systems','Relational databases, SQL, and data modelling.'),
('Web Development','Frontend, backend, and web application development.'),
('Computer Networks','Network protocols, architectures, and communication.')
ON DUPLICATE KEY UPDATE description=VALUES(description);
INSERT INTO categories(name) VALUES ('Lecture Notes'),('Summaries'),('Past Papers'),('Tutorials'),('Presentations'),('Lab Sheets') ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Keep IDs stable by looking up email/name/subject rather than assuming auto-increment values.
INSERT INTO study_groups(name,description,subject_id,creator_id)
SELECT v.name,v.description,s.id,u.id FROM (
 SELECT 'DSA Problem-Solving Circle' name,'Weekly practice for arrays, linked lists, stacks, queues, searching, sorting, and Big-O. Bring one question and explain your approach to the group.' description,'Algorithms & Data Structures' subject,'nethmi.demo@studycircle.test' email
 UNION ALL SELECT 'SQL & Database Design Lab','A friendly group for ER diagrams, normalisation, joins, constraints, and hands-on MySQL exercises. Beginners welcome.','Database Management Systems','kavindu.demo@studycircle.test'
 UNION ALL SELECT 'Web Dev Build-Along','Build small responsive pages, practise PHP forms, and connect features to MySQL. Members share progress and review each other’s code.','Web Development','amaya.demo@studycircle.test'
 UNION ALL SELECT 'OOP Concepts Study Crew','Revise classes, objects, encapsulation, inheritance, polymorphism, and UML with short examples and quiz questions.','Object Oriented Programming','ravindu.demo@studycircle.test'
 UNION ALL SELECT 'AI Foundations Reading Group','Discuss search algorithms, intelligent agents, and introductory machine-learning concepts using beginner-friendly notes.','Artificial Intelligence','nethmi.demo@studycircle.test'
 UNION ALL SELECT 'Computer Networks Revision','Review OSI/TCP-IP models, addressing, routing, DNS, HTTP, and common network troubleshooting scenarios.','Computer Networks','kavindu.demo@studycircle.test'
) v JOIN subjects s ON s.name=v.subject JOIN users u ON u.email=v.email
WHERE NOT EXISTS (SELECT 1 FROM study_groups g WHERE g.name=v.name);

INSERT IGNORE INTO group_members(group_id,user_id)
SELECT g.id,u.id FROM study_groups g JOIN users u ON u.email IN ('student@studycircle.test','nethmi.demo@studycircle.test','kavindu.demo@studycircle.test','amaya.demo@studycircle.test','ravindu.demo@studycircle.test')
WHERE g.name IN ('DSA Problem-Solving Circle','SQL & Database Design Lab','Web Dev Build-Along','OOP Concepts Study Crew','AI Foundations Reading Group','Computer Networks Revision')
AND (g.name <> 'DSA Problem-Solving Circle' OR u.email IN ('nethmi.demo@studycircle.test','student@studycircle.test','ravindu.demo@studycircle.test'))
AND (g.name <> 'SQL & Database Design Lab' OR u.email IN ('kavindu.demo@studycircle.test','student@studycircle.test','amaya.demo@studycircle.test'));

-- Resources reference bundled PDFs copied into uploads/resources by the README instructions.
INSERT INTO resources(title,description,file_path,file_type,subject_id,category_id,uploader_id,status)
SELECT v.title,v.description,v.path,'pdf',s.id,c.id,u.id,'approved'
FROM (
 SELECT 'Algorithms Quick Revision Sheet' title,'Demo revision notes on Big-O, common data structures, searching, sorting, and practice prompts.' description,'uploads/resources/algorithms_quick_revision.pdf' path,'Algorithms & Data Structures' subject,'Summaries' category,'nethmi.demo@studycircle.test' email
 UNION ALL SELECT 'DBMS & SQL Study Sheet','Demo notes on relational tables, keys, joins, aggregate queries, and database design checks.','uploads/resources/database_sql_study_sheet.pdf','Database Management Systems','Lecture Notes','kavindu.demo@studycircle.test'
 UNION ALL SELECT 'HTML, CSS & PHP Revision Notes','Demo checklist for frontend responsibilities, secure PHP forms, PDO, MySQL, accessibility, and responsive UI.','uploads/resources/web_development_revision.pdf','Web Development','Lecture Notes','amaya.demo@studycircle.test'
 UNION ALL SELECT 'OOP Concepts Summary','Demo study sheet covering classes, objects, encapsulation, inheritance, polymorphism, and design reminders.','uploads/resources/oop_concepts_summary.pdf','Object Oriented Programming','Summaries','ravindu.demo@studycircle.test'
) v JOIN subjects s ON s.name=v.subject JOIN categories c ON c.name=v.category JOIN users u ON u.email=v.email
WHERE NOT EXISTS (SELECT 1 FROM resources r WHERE r.title=v.title AND r.uploader_id=u.id);

INSERT INTO questions(user_id,subject_id,title,body,status)
SELECT u.id,s.id,'When should I choose a linked list over an array?','I understand the basic definitions, but I am unsure how to compare their trade-offs for insertions, access by index, and memory usage. What examples help make the choice clear?','visible'
FROM users u JOIN subjects s ON s.name='Algorithms & Data Structures' WHERE u.email='nethmi.demo@studycircle.test'
AND NOT EXISTS (SELECT 1 FROM questions q WHERE q.title='When should I choose a linked list over an array?');
