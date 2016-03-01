#include "stdio.h"
#include "metis.h"
#include "unistd.h"

#define CONFIGFILE "config.ini"
#define DATAFILE "data.csr"

int getconfig(int * config);
int setoptions(int * config);

char optarray[500];

int mode, meshmode, graphmode;
int n = 0, ncon = 0, *xadj = NULL, *adjncy = NULL, *vsize = NULL, *vwgt = NULL, *adjwgt = NULL, nparts, edgecut = 0, *partition = NULL, *epart = NULL, *npart = NULL, m = 0, ncommon = 1;
int ne, nn, *eptr, *eind; 

int main(){
	int config[9], i;
	char temp[50],filename[] = DATAFILE;
	//Here we read the configuration file and show an error if reading it was not possible
	if(getconfig(config)==0){
		printf("Config file not properly loaded\n");
		return -1;
	}
	//Here the configuration options are checked and the command is built, and print an error if
		//the config file data is not correct
	if(setoptions(config) == 0){
		printf("Incorrect configuration file\n");
		return -1;
	}
	//Here we chain the call to metis with the options string
	char command[500];
	sprintf(command,"");
	strcat(command, optarray);
	strcat(command, filename);
	sprintf(temp," %d",nparts);
	strcat(command, temp);	
	strcat(command, "\n");	
	printf(command);
	system(command);
	//Here we set the output filenames to the standard names 
	if(mode == 0){
		sprintf(command, "mv data.csr.part.%d output.csr\n", nparts);
		system(command);
	}
	else if(mode == 1){
		sprintf(command, "mv data.csr.npart.%d noutput.csr\n", nparts);
		system(command);
		sprintf(command, "mv data.csr.epart.%d eoutput.csr\n", nparts);
		system(command);
	}

}

/* This function reads the configuration file and loads it into 
*  an integer array
*  @return Returns 0 if the file was not possible to read and 1 if successful
*/
int getconfig(int * config){
	FILE * fp;
	int i;
	char *temp, line[100];	
	temp = (char*) malloc(sizeof(char) * 20);
	fp = fopen(CONFIGFILE,"r");
	if(fp==NULL)
		return 0;
	fgets(line, 1000, fp);
	temp = strtok(line, " ");
	for(i=0;i<10 && temp!=NULL;i++){
		config[i]=atoi(temp);
		temp = strtok(NULL, " ");
		printf(temp);		
	}
	fclose(fp);
	printf("\nConfiguration file: ");	
	for(i=0;i<10;i++)
		printf("%d ",config[i]);
	printf("\n");
	return 1;
}

/* This function creates the configuration string based on the integer
*  array previously loaded
*  If any of the parameters does not fill the expected alues, prints an error with
*  the failed option
*  @return Returns 0 if the file was not possible to read and 1 if successful
*/
int setoptions(int * config){
	char temp[100];
	if(config[0]==1){
		sprintf(optarray,"mpmetis ");
		switch(config[1]){
			//Only makes sense for graphs
			case 0:
				strcat(optarray, "-ptype=rb ");
				break;
			case 1:
				strcat(optarray, "-ptype=kway ");
				break;
			default:
				printf("PTYPE value is not valid\n");
				return 0;
				break;
		}
	}
	else{
		sprintf(optarray,"gpmetis ");
		switch(config[1]){
			case 0:
				strcat(optarray, "-ptype=rm ");
				graphmode = 0;
				break;
			case 1:
				strcat(optarray, "-ptype=kway ");
				graphmode = 1;
				break;
			default:
				printf("Graph value is not valid\n");
				return 0;
				break;
		}
		
	}
		switch(config[2]){
			case 0:
				strcat(optarray, "-objtype=cut ");
				break;
			case 1:
				strcat(optarray, "-objtype=vol ");
				break;
			default:
				printf("OBJTYPE value is not valid\n");
				return 0;
				break;
		}
	switch(config[3]){
		case 0:
			strcat(optarray, "-ctype=rm ");
			break;
		case 1:
			strcat(optarray, "-ctype=shem ");
			break;
		default:
			printf("CTYPE value is not valid\n");
			return 0;
			break;
	}
	switch(config[4]){
		case 0:
			strcat(optarray, "-iptype=grow ");			
			break;
		case 1:
			strcat(optarray, "-iptype=random ");
			break;
		default:
			printf("IPTYPE value is not valid\n");
			return 0;
			break;
	}
	switch(config[6]){
		//Only applies for kway
		case 0:
			break;
		case 1:
			strcat(optarray, "-contig ");
			break;
		default:
			printf("CONTIG value is not valid\n");
			return 0;
			break;
	}
	if(config >= 0){
		sprintf(temp,"-ufactor=%d ", config[7]);
		strcat(optarray, temp);
	}
	else{
		printf("UFACTOR value is not valid\n");
		return 0;
	}
	if(config[8] > 0)
		nparts = config[8];
	else {
		printf("Input error, incorrect nparts");
		return 0;
	}
	switch(config[0]){
		case 0:
			mode = 0;
		break;
		case 1:
			mode = 1;
			switch(config[9]){
				case 0: 
					meshmode = 0;
					strcat(optarray, "-gtype=dual ");
					break;
				case 1:
					meshmode = 1;
					strcat(optarray, "-gtype=nodal ");
					break;
				default:
					printf("MESH MODE value is not valid = %d\n", config[9]);
					return 0;
					break;
			}
			break;
		default:
			printf("MODE value is not valid\n");
			return 0;
			break;
	}
	return 1;
}


